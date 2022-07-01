<?php

namespace app\services;

use app\common\constants\Confirm;
use app\forms\UserForm;
use app\models\business\Authorization;
use app\models\business\User;
use app\common\constants\UserState;
use app\common\utils\Code;
use app\common\utils\CoordinateTransform;
use app\common\utils\Result;
use app\common\utils\Excel;
use app\common\utils\Pagination;
use app\repositories\UserRepository;
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
 * 用户服务
 */
class UserService
{
    /**
     * 用户所有记录
     * @param array $params 查询参数
     * @return Result
     */
    public function items(array $params = []): Result
    {
        $repo = Yii::$container->get(UserRepository::class);
        /** @var User[] $records */
        $records = $repo->buildCondition($params)->orderBy(['id' => \SORT_DESC])->all();
        $items = [];
        foreach ($records as $record) {
            $items[] = $record->toArray();
        }
        return Result::success()->setData([
            'items' => $items
        ]);
    }

    /**
     * 用户分页记录
     * @param array $params 查询参数
     * @return Result
     */
    public function pageItems(array $params = []): Result
    {
        $pagination = new Pagination();
        $repo = Yii::$container->get(UserRepository::class);
        $query = $repo->buildCondition($params)->orderBy(['id' => \SORT_DESC]);
        /** @var User[] $records */
        $records = $repo->pagination($pagination, $query)->all();
        $items = [];
        foreach ($records as $record) {
            $item = $record->toArray();
            unset($item['password']);
            $items[] = $item;
        }
        $data = [
            'total' => (int)$pagination->totalCount,
            'items' => $items
        ];
        return Result::success()->setData($data);
    }

    /**
     * 用户详情
     * @param int $id 主键ID
     * @return Result
     */
    public function detail(int $id): Result
    {
        /** @var User $item */
        $user = User::find()->where(['id' => $id])->limit(1)->one();
        if (!$user) {
            throw new NotFoundHttpException('不存在此记录');
        }
        return Result::success()->setData([
            'item' => $user->toArray()
        ]);
    }

    /**
     * 创建用户
     * @param array $data
     * @return Result
     */
    public function create(array $data): Result
    {
        //表单
        $form = new UserForm();
        $form->scenario = 'create';
        $form->attributes = $data;
        if (!$form->validate()) {
            throw new UnprocessableEntityHttpException(implode(',', $form->getErrorSummary(false)));
        }
        //新增用户
        $user = new User();
        $user->setCamelAttributes($data);
        if (!$user->validate()) {
            throw new UnprocessableEntityHttpException($user->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //保存用户
            $user->password = Yii::$app->security->generatePasswordHash($user->password);
            if (!$user->save(false)) {
                throw new ServerErrorHttpException('创建失败');
            }
            //保存用户绑定角色
            foreach ($form->roleIds as $roleId) {
                Yii::$app->db->createCommand()
                    ->insert(
                        'user_role',
                        [
                            'user_id' => $user->id,
                            'role_id' => $roleId
                        ]
                    )
                    ->execute();
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 更新用户
     * @param array $data 新的数据
     * @return Result
     */
    public function update(array $data): Result
    {
        if (!isset($data['id']) || !is_numeric($data['id'])) {
            throw new UnprocessableEntityHttpException('数据缺失');
        }
        $id = (int)$data['id'];
        /** @var User $user */
        $user = User::find()->where(['id' => $id])->limit(1)->one();
        if (!$user) {
            throw new NotFoundHttpException('没有找到记录');
        }
        //表单
        $form = new UserForm();
        $form->attributes = $data;
        if (!$form->validate()) {
            throw new UnprocessableEntityHttpException(implode(',', $form->getErrorSummary(false)));
        }
        //模型
        $user->setCamelAttributes($data);
        if (!$user->validate()) {
            throw new UnprocessableEntityHttpException($user->validatorError);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$user->save(false)) {
                throw new ServerErrorHttpException('更新失败');
            }
            //移除绑定的角色
            Yii::$app->db->createCommand()
                ->delete('user_role', ['user_id' => $user->id])
                ->execute();
            //保存用户绑定角色
            foreach ($form->roleIds as $roleId) {
                Yii::$app->db->createCommand()
                    ->insert(
                        'user_role',
                        [
                            'user_id' => $user->id,
                            'role_id' => $roleId
                        ]
                    )
                    ->execute();
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

    /**
     * 删除用户
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
            /** @var User $user */
            $user = User::findOne($id);
            if (!$user) {
                throw new NotFoundHttpException('没有找到记录');
            }
            $models[] = $user;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($models as $model) {
                $model->delete();
                //删除令牌
                Authorization::deleteAll(['user_id' => $model->id]);
                //删除绑定的角色
                Yii::$app->db->createCommand()
                    ->delete('user_role', ['user_id' => $model->id])
                    ->execute();
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return Result::success();
    }

}
