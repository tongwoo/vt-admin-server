<?php

namespace app\controllers;

use app\common\utils\Code;
use app\common\utils\Result;
use app\services\RouteService;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * 路由
 */
class RouteController extends BaseController
{
    public function getLoginOptional(): array
    {
        return [
            'generate'
        ];
    }


    /**
     * RouteService
     */
    private ?RouteService $service = null;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->service = Yii::$container->get(RouteService::class);
    }

    /**
     * 路由所有记录
     * @return Response
     */
    public function actionItems(): Response
    {
        $params = $this->request->get();
        return $this->service->items($params)->asJsonResponse();
    }

    /**
     * 路由分页记录
     * @return Response
     */
    public function actionPageItems(): Response
    {
        $params = $this->request->get();
        return $this->service->pageItems($params)->asJsonResponse();
    }

    /**
     * 路由详情
     * @return Response
     */
    public function actionDetail(): Response
    {
        $id = (int)$this->request->get('id');
        return $this->service->detail($id)->asJsonResponse();
    }

    /**
     * 创建路由
     * @return Response
     */
    public function actionCreate(): Response
    {
        $data = $this->request->post();
        return $this->service->create($data)->asJsonResponse();
    }

    /**
     * 更新路由
     * @return Response
     */
    public function actionUpdate(): Response
    {
        $data = $this->request->post();
        return $this->service->update($data)->asJsonResponse();
    }

    /**
     * 删除路由
     * @return Response
     */
    public function actionDelete(): Response
    {
        $ids = $this->request->post('ids');
        return $this->service->delete($ids)->asJsonResponse();
    }

    /**
     * 清空路由
     * @return Response
     */
    public function actionTruncate(): Response
    {
        try {
            $this->service->truncate();
        } catch (\Exception $e) {
            return Result::failure($e->getMessage())->asJsonResponse();
        }
        return Result::success()->asJsonResponse();
    }

    /**
     * 自动创建
     * @return Response
     */
    public function actionGenerate(): Response
    {
        try {
            $this->service->createFromDir(__DIR__);
        } catch (\Exception $e) {
            return Result::failure($e->getMessage())->asJsonResponse();
        }
        return Result::success()->asJsonResponse();
    }
}
