<?php

namespace app\controllers;

use app\common\utils\Code;
use app\common\utils\Result;
use app\services\PermissionService;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * 权限
 */
class PermissionController extends BaseController
{
    /**
     * PermissionService
     */
    private ?PermissionService $service = null;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->service = Yii::$container->get(PermissionService::class);
    }

    /**
     * 权限所有记录
     * @return Response
     */
    public function actionItems(): Response
    {
        $params = $this->request->get();
        return $this->service->items($params)->asJsonResponse();
    }

    /**
     * 权限分页记录
     * @return Response
     */
    public function actionPageItems(): Response
    {
        $params = $this->request->get();
        return $this->service->pageItems($params)->asJsonResponse();
    }

    /**
     * 权限详情
     * @return Response
     */
    public function actionDetail(): Response
    {
        $id = (int)$this->request->get('id');
        return $this->service->detail($id)->asJsonResponse();
    }

    /**
     * 创建权限
     * @return Response
     */
    public function actionCreate(): Response
    {
        $data = $this->request->post();
        return $this->service->create($data)->asJsonResponse();
    }

    /**
     * 更新权限
     * @return Response
     */
    public function actionUpdate(): Response
    {
        $data = $this->request->post();
        return $this->service->update($data)->asJsonResponse();
    }

    /**
     * 删除权限
     * @return Response
     */
    public function actionDelete(): Response
    {
        $ids = $this->request->post('ids');
        return $this->service->delete($ids)->asJsonResponse();
    }

    /**
     * 权限树
     * @return Response
     */
    public function actionTree(): Response
    {
        $params = $this->request->get();
        return $this->service->tree($params)->asJsonResponse();
    }

    /**
     * 规则列表
     */
    public function actionRules(): Response
    {
        $manager = Yii::$app->authManager;
        $rules = [];
        foreach ($manager->getRules() as $rule) {
            $rules[] = [
                'name' => $rule->name,
                'value' => $rule->name
            ];
        }
        return Result::success()->setData([
            'items' => $rules
        ])->asJsonResponse();
    }
}
