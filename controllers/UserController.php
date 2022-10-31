<?php

namespace app\controllers;

use app\common\utils\Code;
use app\common\utils\Result;
use app\services\UserService;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * 用户
 */
class UserController extends BaseController
{
    /**
     * UserService
     */
    private ?UserService $service = null;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->service = Yii::$container->get(UserService::class);
    }

    /**
     * 用户所有记录
     * @return Response
     */
    public function actionItems(): Response
    {
        $params = $this->request->get();
        return $this->service->items($params)->asJsonResponse();
    }

    /**
     * 用户分页记录
     * @return Response
     */
    public function actionPageItems(): Response
    {
        $params = $this->request->get();
        return $this->service->pageItems($params)->asJsonResponse();
    }

    /**
     * 用户详情
     * @return Response
     */
    public function actionDetail(): Response
    {
        $id = (int)$this->request->get('id');
        return $this->service->detail($id)->asJsonResponse();
    }

    /**
     * 创建用户
     * @return Response
     */
    public function actionCreate(): Response
    {
        $data = $this->request->post();
        return $this->service->create($data)->asJsonResponse();
    }

    /**
     * 更新用户
     * @return Response
     */
    public function actionUpdate(): Response
    {
        $data = $this->request->post();
        return $this->service->update($data)->asJsonResponse();
    }

    /**
     * 删除用户
     * @return Response
     */
    public function actionDelete(): Response
    {
        $ids = $this->request->post('ids');
        return $this->service->delete($ids)->asJsonResponse();
    }

    /**
     * 修改密码
     * @return Response
     */
    public function actionUpdatePassword()
    {
        return Result::success()->asJsonResponse();
    }
}
