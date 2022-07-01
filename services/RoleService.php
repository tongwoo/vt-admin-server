<?php

namespace app\services;

use app\models\business\Permission;
use app\models\business\Role;
use app\common\utils\Result;
use app\common\utils\Pagination;
use app\repositories\RoleRepository;
use Throwable;
use Yii;
use yii\validators\EachValidator;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * 角色服务
 */
class RoleService
{
    /**
     * 角色所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function items(array $params = []): Result
    {
        $repo = Yii::$container->get(RoleRepository::class);
        /** @var Role[] $records */
        $records = $repo->findByParams($params);
        $items = [];
        foreach ($records as $record) {
            $items[] = $record->toArray();
        }
        return Result::success()->setData([
            'items' => $items
        ]);
    }

    /**
     * 角色分页记录
     * @param array $params 查询参数
     * @return Result
     */
    public function pageItems(array $params = []): Result
    {
        $pagination = new Pagination();
        $repo = Yii::$container->get(RoleRepository::class);
        $query = $repo->buildCondition($params);
        /** @var Role[] $records */
        $records = $repo->pagination($pagination, $query)->all();
        $items = [];
        foreach ($records as $record) {
            $items[] = $record->toArray();
        }
        $data = [
            'total' => (int)$pagination->totalCount,
            'items' => $items
        ];
        return Result::success()->setData($data);
    }

    /**
     * 角色详情
     * @param int $id 主键ID
     * @return Result
     */
    public function detail(int $id): Result
    {
        /** @var Role $item */
        $role = Role::find()->where(['id' => $id])->limit(1)->one();
        if (!$role) {
            throw new NotFoundHttpException('不存在此记录');
        }
        return Result::success()->setData([
            'item' => $role->toArray()
        ]);
    }

    /**
     * 创建角色
     * @param array $data
     * @return Result
     */
    public function create(array $data): Result
    {
        //新增角色
        $role = new Role();
        $role->setCamelAttributes($data);
        if (!$role->validate()) {
            throw new UnprocessableEntityHttpException($role->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$role->save(false)) {
                throw new ServerErrorHttpException('创建失败');
            }
            //RBAC
            $manager = Yii::$app->authManager;
            $manager->add($role->getRbacRole());
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 更新角色
     * @param array $data 新的数据
     * @return Result
     */
    public function update(array $data): Result
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $id = (int)$data['id'];
        /** @var Role $role */
        $role = Role::find()->where(['id' => $id])->limit(1)->one();
        if (!$role) {
            throw new NotFoundHttpException('没有找到记录');
        }
        $legacyName = $role->name;
        $role->setCamelAttributes($data);
        if (!$role->validate()) {
            throw new UnprocessableEntityHttpException($role->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$role->save(false)) {
                throw new ServerErrorHttpException('更新失败');
            }
            //RBAC
            $manager = Yii::$app->authManager;
            $manager->update($legacyName, $role->getRbacRole());
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 删除角色
     * @param string|int $id 主键ID
     * @return Result
     */
    public function delete($id): Result
    {
        $ids = is_numeric($id) ? [$id] : explode(',', $id);
        if (count($ids) === 0 || $ids[0] === '') {
            throw new UnprocessableEntityHttpException('参数无效');
        }
        $models = [];
        foreach ($ids as $id) {
            $id = (int)$id;
            if (!$id) {
                throw new UnprocessableEntityHttpException('参数ID无效');
            }
            /** @var Role $role */
            $role = Role::findOne($id);
            if (!$role) {
                throw new NotFoundHttpException('没有找到记录');
            }
            //是否还存在未删除的角色权限
            $exists = $role->getPermissions()->exists();
            if ($exists) {
                throw new UnprocessableEntityHttpException('还有未删除的权限，请先删除权限');
            }
            //是否还存在未删除的角色用户
            $exists = $role->getUsers()->exists();
            if ($exists) {
                throw new UnprocessableEntityHttpException('还有未删除的用户，请先删除用户');
            }
            $models[] = $role;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($models as $model) {
                $model->delete();
                //删除绑定
                Yii::$app->db->createCommand()
                    ->delete('role_permission', ['role_id' => $model->id])
                    ->execute();
                //RBAC
                Yii::$app->authManager->remove($model->getRbacRole());
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }


    /**
     * 更新角色
     * @param array $data 新的数据
     * @return Result
     */
    public function bind(array $data): Result
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $id = (int)$data['id'];
        if (!isset($data['permissions']) || !is_array($data['permissions'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $permissionIds = $data['permissions'];
        $validator = new EachValidator();
        $validator->rule = ['integer'];
        if (!$validator->validate($permissionIds)) {
            throw new UnprocessableEntityHttpException('权限参数有误');
        }
        if (count($permissionIds) > 100) {
            throw new UnprocessableEntityHttpException('权限参数有误');
        }
        $role = Role::find()->where(['id' => $id])->one();
        if (!$role) {
            throw new NotFoundHttpException('不存在此角色');
        }
        $permissions = Permission::find()->where(['id' => $permissionIds])->all();
        if (count($permissions) !== count($permissionIds)) {
            throw new NotFoundHttpException('部分权限不存在');
        }
        $manager = Yii::$app->authManager;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $rbacRole = $role->getRbacRole();
            //移除之前的绑定
            Yii::$app->db->createCommand()
                ->delete('role_permission', 'role_id')
                ->execute();
            $manager->removeChildren($rbacRole);
            //添加新的绑定
            foreach ($permissions as $permission) {
                Yii::$app->db->createCommand()
                    ->insert(
                        'role_permission',
                        [
                            'role_id' => $id,
                            'permission_id' => $permission->id
                        ]
                    )
                    ->execute();
                $manager->addChild($rbacRole, $permission->getRbacPermission());
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            return Result::failure($e->getMessage());
        }
        return Result::success();
    }
}
