<?php

namespace app\behaviors;

use app\controllers\BaseController;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UnauthorizedHttpException;

/**
 * 用户认证
 */
class HttpBearerBehavior extends HttpBearerAuth
{
    /**
     * @inheritDoc
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('凭据无效');
    }

    /**
     * @inheritDoc
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action): bool
    {
        /** @var BaseController $controller */
        $controller = $this->owner;
        $this->optional = $controller->getLoginOptional();
        return parent::beforeAction($action);
    }
}
