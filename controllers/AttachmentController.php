<?php

namespace app\controllers;

use app\common\utils\Code;
use app\common\utils\Result;
use app\services\AttachmentService;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * 附件
 */
class AttachmentController extends BaseController
{
    /**
     * AttachmentService
     */
    private ?AttachmentService $service = null;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->service = Yii::$container->get(AttachmentService::class);
    }

    /**
     * 附件所有记录
     * @return Response
     */
    public function actionItems(): Response
    {
        $params = $this->request->get();
        return $this->service->items($params)->asJsonResponse();
    }

    /**
     * 附件分页记录
     * @return Response
     */
    public function actionPageItems(): Response
    {
        $params = $this->request->get();
        return $this->service->pageItems($params)->asJsonResponse();
    }

    /**
     * 附件详情
     * @return Response
     */
    public function actionDetail(): Response
    {
        $id = (int)$this->request->get('id');
        return $this->service->detail($id)->asJsonResponse();
    }

    /**
     * 创建附件
     * @return Response
     */
    public function actionCreate(): Response
    {
        $data = $this->request->post();
        return $this->service->create($data)->asJsonResponse();
    }

    /**
     * 更新附件
     * @return Response
     */
    public function actionUpdate(): Response
    {
        $data = $this->request->post();
        return $this->service->update($data)->asJsonResponse();
    }

    /**
     * 删除附件
     * @return Response
     */
    public function actionDelete(): Response
    {
        $ids = $this->request->post('ids');
        return $this->service->delete($ids)->asJsonResponse();
    }
}
