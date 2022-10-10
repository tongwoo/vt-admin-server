<?php

namespace app\controllers;

use app\behaviors\HttpBearerBehavior;
use app\behaviors\PermissionBehavior;
use yii\filters\RateLimiter;
use yii\web\Controller;

/**
 * 基础控制器
 */
class BaseController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        //登录检查
        $behaviors['login'] = [
            'class' => HttpBearerBehavior::class,
        ];
        //权限检查
        $behaviors['permission'] = [
            'class' => PermissionBehavior::class,
        ];
        //速率限制
        $behaviors['limit'] = [
            'class' => RateLimiter::class,
        ];
        return $behaviors;
    }


    /**
     * 不需要登录验证的action列表，但是仍然走认证过程
     * @return array
     */
    public function getLoginOptional(): array
    {
        return [];
    }
}
