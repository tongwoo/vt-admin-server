<?php

namespace app\controllers;

use app\common\utils\Code;
use app\common\utils\Result;
use app\models\business\Permission;
use app\models\business\Role;
use app\services\RoleService;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

/**
 * 角色
 */
class RoleController extends BaseController
{
    /**
     * RoleService
     */
    private ?RoleService $service = null;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->service = Yii::$container->get(RoleService::class);
    }

    /**
     * 角色所有记录
     * @return Response
     */
    public function actionItems(): Response
    {
        $params = $this->request->get();
        return $this->service->items($params)->asJsonResponse();
    }

    /**
     * 角色分页记录
     * @return Response
     */
    public function actionPageItems(): Response
    {
        $params = $this->request->get();
        return $this->service->pageItems($params)->asJsonResponse();
    }

    /**
     * 角色详情
     * @return Response
     */
    public function actionDetail(): Response
    {
        $id = (int)$this->request->get('id');
        return $this->service->detail($id)->asJsonResponse();
    }

    /**
     * 创建角色
     * @return Response
     */
    public function actionCreate(): Response
    {
        $data = $this->request->post();
        return $this->service->create($data)->asJsonResponse();
    }

    /**
     * 更新角色
     * @return Response
     */
    public function actionUpdate(): Response
    {
        $data = $this->request->post();
        return $this->service->update($data)->asJsonResponse();
    }

    /**
     * 删除角色
     * @return Response
     */
    public function actionDelete(): Response
    {
        $ids = $this->request->post('ids');
        return $this->service->delete($ids)->asJsonResponse();
    }

    /**
     * 角色下的权限列表
     * @return Response
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function actionPermissions(): Response
    {
        $id = $this->request->get('id');
        if (!$id) {
            throw new UnprocessableEntityHttpException('参数缺失');
        }
        $role = Role::find()->where(['id' => $id])->one();
        if (!$role) {
            throw new NotFoundHttpException('不存在此角色');
        }
        $permissions = array_map(function (Permission $permission) {
            return $permission->toArray(['id', 'name', 'description']);
        }, $role->permissions);
        return Result::success()->setData([
            'items' => $permissions
        ])->asJsonResponse();
    }

    /**
     * 绑定权限
     * @return Response
     */
    public function actionBind(): Response
    {
        $data = $this->request->post();
        return $this->service->bind($data)->asJsonResponse();
    }
}
