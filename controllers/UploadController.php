<?php

namespace app\controllers;

use app\common\utils\Result;
use app\services\UploadService;
use Exception;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * 上传相关
 */
class UploadController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function getLoginOptional(): array
    {
        return [
            'common'
        ];
    }

    /**
     * 上传文件
     */
    public function actionCommon(): Response
    {
        $service = Yii::$container->get(UploadService::class);
        return Result::success()->setData($service->common('file'))->asJsonResponse();
    }

    /**
     * 上传头像
     * @return Response
     */
    public function actionAvatar(): Response
    {
        $service = Yii::$container->get(UploadService::class);
        return Result::success()->setData($service->avatar('file'))->asJsonResponse();
    }

    /**
     * 编辑器文件上传
     * @return array
     */
    public function actionEditor(): array
    {
        $service = Yii::$container->get(UploadService::class);
        $this->response->format = Response::FORMAT_JSON;
        try {
            $result = $service->common('upload');
        } catch (HttpException $e) {
            $this->response->statusCode = $e->statusCode;
            return [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
        return $result;
    }
}
