<?php

namespace app\behaviors;

use app\controllers\BaseController;
use app\models\business\Route;
use Yii;
use yii\base\Action;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\base\Controller;
use yii\base\Event;
use yii\web\ForbiddenHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * 权限检测
 */
class PermissionBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    public function events(): array
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'checkPermission'
        ];
    }

    /**
     * 检查权限
     * @param ActionEvent $event 事件
     * @return bool
     */
    public function checkPermission(ActionEvent $event): bool
    {
        /** @var BaseController $action */
        $controller = $event->sender;
        $path = '/' . $controller->id . '/' . $event->action->id;
        $route = Route::find()
            ->where(['path' => $path])
            ->one();
        if (!$route) {
            return true;
        }
        if ($route->permission === null) {
            return true;
        }
        $user = Yii::$app->user;
        if ($user->isGuest) {
            throw new UnauthorizedHttpException('非法用户');
        }
        if (!Yii::$app->user->can($route->permission->name)) {
            throw new ForbiddenHttpException('权限不足');
        }
        return true;
    }

}
