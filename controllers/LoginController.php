<?php

namespace app\controllers;

use app\common\constants\HttpMethod;
use app\common\utils\Result;
use app\models\business\Authorization;
use app\models\business\User;
use Exception;
use Yii;
use yii\captcha\CaptchaAction;
use yii\captcha\CaptchaValidator;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * 登录相关
 */
class LoginController extends BaseController
{
    /**
     * @inheritDoc
     */
    public function getLoginOptional(): array
    {
        return [
            'index',
            'exit',
            'captcha'
        ];
    }

    /**
     * @inheritDoc
     */
    public function actions(): array
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::class,
                'maxLength' => 4,
                'minLength' => 4
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action): bool
    {
        /**@var CaptchaAction $action */
        if ($action->id === 'captcha') {
            $action->getVerifyCode(true);
        }
        return parent::beforeAction($action);
    }

    /**
     * 账户登录
     */
    public function actionIndex(): Response
    {
        $username = $this->request->post('username');
        $password = $this->request->post('password');
        /**
        $captcha = $this->request->post('captcha');
        $validator = new CaptchaValidator();
        $validator->captchaAction = 'login/captcha';
        if (!$validator->validate($captcha)) {
            Result::failure('验证码不正确')->asJsonResponse();
        }
        */
        try {
            $result = User::loginByAccount([$username, $password]);
        } catch (Exception $e) {
            return Result::failure($e->getMessage())->asJsonResponse();
        }
        return Result::success()->setData($result)->asJsonResponse();
    }

    /**
     * 退出登录
     * @return Response
     */
    public function actionExit(): Response
    {
        $userId = Yii::$app->user->id;
        if ($userId) {
            Authorization::deleteAll(['user_id' => $userId]);
            Yii::$app->user->logout();
        }
        return Result::success('退出成功')->asJsonResponse();
    }
}
