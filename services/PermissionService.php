<?php

namespace app\services;

use app\common\constants\Confirm;
use app\models\business\Permission;
use app\common\utils\Code;
use app\common\utils\CoordinateTransform;
use app\common\utils\Result;
use app\common\utils\Excel;
use app\common\utils\Pagination;
use app\repositories\PermissionRepository;
use app\repositories\RoleRepository;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * 权限服务
 */
class PermissionService
{
    /**
     * 权限所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function items(array $params = []): Result
    {
        $repo = Yii::$container->get(PermissionRepository::class);
        /** @var Permission[] $records */
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
     * 权限分页记录
     * @param array $params 查询参数
     * @return Result
     */
    public function pageItems(array $params = []): Result
    {
        $pagination = new Pagination();
        $repo = Yii::$container->get(PermissionRepository::class);
        $query = $repo->buildCondition($params);
        /** @var Permission[] $records */
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
     * 权限详情
     * @param int $id 主键ID
     * @return Result
     */
    public function detail(int $id): Result
    {
        /** @var Permission $item */
        $permission = Permission::find()->where(['id' => $id])->limit(1)->one();
        if (!$permission) {
            throw new NotFoundHttpException('不存在此记录');
        }
        return Result::success()->setData([
            'item' => $permission->toArray()
        ]);
    }

    /**
     * 创建权限
     * @param array $data
     * @return Result
     */
    public function create(array $data): Result
    {
        //新增权限
        $permission = new Permission();
        $permission->setCamelAttributes($data);
        if (!$permission->validate()) {
            throw new UnprocessableEntityHttpException($permission->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$permission->save(false)) {
                throw new ServerErrorHttpException('创建失败');
            }
            $manager = Yii::$app->authManager;
            $manager->add($permission->getRbacPermission());
            //是否包含了子权限
            if (isset($data['include']) && $data['include']) {
                $description = $permission->description;
                $description = str_replace('管理', '', $description);
                $children = [
                    [
                        'name' => $permission->name . '_create',
                        'description' => '创建' . $description
                    ],
                    [
                        'name' => $permission->name . '_read',
                        'description' => '查看' . $description
                    ],
                    [
                        'name' => $permission->name . '_update',
                        'description' => '修改' . $description
                    ],
                    [
                        'name' => $permission->name . '_delete',
                        'description' => '删除' . $description
                    ],
                ];
                foreach ($children as $child) {
                    $childPermission = new Permission();
                    $childPermission->parentId = $permission->id;
                    $childPermission->name = $child['name'];
                    $childPermission->description = $child['description'];
                    if (!$childPermission->save(false)) {
                        throw new ServerErrorHttpException('创建子权限失败');
                    }
                    $manager->add($childPermission->getRbacPermission());
                }
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 更新权限
     * @param array $data 新的数据
     * @return Result
     */
    public function update(array $data): Result
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $id = (int)$data['id'];
        /** @var Permission $permission */
        $permission = Permission::find()->where(['id' => $id])->limit(1)->one();
        if (!$permission) {
            throw new NotFoundHttpException('没有找到记录');
        }
        $oldName = $permission->name;
        $permission->setCamelAttributes($data);
        if (!$permission->validate()) {
            throw new UnprocessableEntityHttpException($permission->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$permission->save(false)) {
                throw new ServerErrorHttpException('更新失败');
            }
            $manager = Yii::$app->authManager;
            $manager->update($oldName, $permission->getRbacPermission());
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 删除权限
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
            /** @var Permission $permission */
            $permission = Permission::findOne($id);
            if (!$permission) {
                throw new NotFoundHttpException('没有找到记录');
            }
            if ($permission->getChildren()->exists()) {
                throw new UnprocessableEntityHttpException('存在子权限，请先删除子权限');
            }
            $models[] = $permission;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($models as $model) {
                $model->delete();
                //删除绑定
                Yii::$app->db->createCommand()
                    ->delete('role_permission', ['permission_id' => $model->id])
                    ->execute();
                //删除RBAC
                Yii::$app->authManager->remove($model->getRbacPermission());
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 权限所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function tree(array $params = []): Result
    {
        $repo = Yii::$container->get(PermissionRepository::class);
        /** @var Permission[] $records */
        $records = $repo->buildCondition()->all();
        $items = Permission::listToTree($records);
        if (isset($params['root'])) {
            array_unshift($items, [
                'id' => 0,
                'description' => '无',
            ]);
        }
        return Result::success()->setData([
            'items' => $items
        ]);
    }

}
