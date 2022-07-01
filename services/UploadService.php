<?php

namespace app\services;

use app\common\constants\Confirm;
use app\common\utils\Result;
use app\models\business\Attachment;
use app\models\business\User;
use http\Exception\InvalidArgumentException;
use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

/**
 * 上传服务
 */
class UploadService
{
    /**
     * 公共文件上传
     * @param string $fileName 文件域名称
     * @return array
     */
    public function common(string $fileName): array
    {
        $uploader = UploadedFile::getInstanceByName($fileName);
        if (!$uploader) {
            throw new UnprocessableEntityHttpException('请上传文件');
        }
        $name = Yii::$app->security->generateRandomString() . '.' . $uploader->extension;
        $dir = date('Y/m/d');
        $saveDir = Yii::$app->basePath . '/web/uploads/' . $dir;
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        if (!is_writable($saveDir)) {
            throw new ServerErrorHttpException('目录没有写权限');
        }
        $savePath = $dir . '/' . $name;
        $saveFullPath = $saveDir . '/' . $name;
        if (!$uploader->saveAs($saveFullPath)) {
            throw new ServerErrorHttpException('保存失败');
        }
        $user = Yii::$app->user;
        $hash = md5($uploader->name . time());
        if ($user->id) {
            $hash = md5($uploader->name . $user->id);
        }
        $attachment = new Attachment();
        $attachment->name = $uploader->baseName;
        $attachment->filename = $uploader->name;
        $attachment->extension = $uploader->extension;
        $attachment->path = $savePath;
        $attachment->time = date('Y-m-d H:i:s');
        $attachment->size = $uploader->size;
        $attachment->hash = $hash;
        $attachment->isValid = Confirm::NO;
        if (!$attachment->save(false)) {
            unlink($saveFullPath);
            throw new ServerErrorHttpException('系统异常');
        }
        return [
            'name' => $uploader->baseName,
            'filename' => $uploader->name,
            'path' => $savePath,
            'hash' => $hash,
            'url' => $_ENV['ASSET_BASE_URL'] . 'uploads/' . $savePath
        ];
    }

    /**
     * 头像上传
     * @param string $fileName 文件域名称
     * @return array
     */
    public function avatar(string $fileName): array
    {
        $uploader = UploadedFile::getInstanceByName($fileName);
        if (!$uploader) {
            throw new UnprocessableEntityHttpException('请上传头像图片');
        }
        $mimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($uploader->type, $mimeTypes)) {
            throw new UnprocessableEntityHttpException('图片格式不正确');
        }
        $name = Yii::$app->security->generateRandomString() . '.' . $uploader->extension;
        $dir = 'avatars/' . date('Y/m/d');
        $saveDir = Yii::$app->basePath . '/web/' . $dir;
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0777, true);
        }
        if (!is_writable($saveDir)) {
            throw new ServerErrorHttpException('目录没有写权限');
        }
        $savePath = $dir . '/' . $name;
        $saveFullPath = $saveDir . '/' . $name;
        if (!$uploader->saveAs($saveFullPath)) {
            throw new ServerErrorHttpException('保存失败');
        }
        $user = Yii::$app->user;
        $hash = md5($uploader->name . time());
        if ($user->id) {
            $hash = md5($uploader->name . $user->id);
        }
        /** @var User $identity */
        $identity = $user->identity;
        $identity->avatar = $savePath;
        if (!$identity->save(false)) {
            unlink($saveFullPath);
            throw new ServerErrorHttpException('系统异常');
        }
        return [
            'url' => $_ENV['ASSET_BASE_URL'] . $savePath
        ];
    }
}
