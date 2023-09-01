<?php

namespace app\controllers;

use app\common\utils\Result;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * 默认控制器
 */
class SiteController extends Controller
{
    /**
     * 默认Action
     */
    public function actionIndex(): Response
    {
        return Result::success()->asJsonResponse();
    }

    /**
     * 错误处理
     * @return Response|string
     */
    public function actionError()
    {
        $response = Yii::$app->response;
        $exception = Yii::$app->errorHandler->exception;
        $message = $exception ? $exception->getMessage() : 'unknown error';
        $contentTypes = $this->request->acceptableContentTypes;
        if (isset($contentTypes['application/json'])) {
            $result = Result::failure($message)->setCode($response->statusCode);
            if (\YII_DEBUG) {
                $result->setData([
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]);
            }
            return $result->asJsonResponse();
        }
        return $this->renderPartial('error', [
            'message' => $message
        ]);
    }
}
