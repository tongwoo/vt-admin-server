<?php

namespace app\models\base;

use app\models\BaseModel;

/**
 * 用户授权
 * @property int    $id
 * @property int    $userId      用户
 * @property string $value       授权值
 * @property string $expiresTime 过期时间
 */
class BaseAuthorization extends BaseModel
{
    /**
     * 表名
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%authorization}}';
    }

    /**
     * 设置用户
     * @param int|null $value 参数值
     */
    public function setUserId(?int $value)
    {
        $this->user_id = $value;
    }

    /**
     * 获取用户
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * 设置授权值
     * @param string|null $value 参数值
     */
    public function setValue(?string $value)
    {
        $this->value = $value;
    }

    /**
     * 获取授权值
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * 设置过期时间
     * @param string|null $value 参数值
     */
    public function setExpiresTime(?string $value)
    {
        $this->expires_time = $value;
    }

    /**
     * 获取过期时间
     * @return string|null
     */
    public function getExpiresTime(): ?string
    {
        return $this->expires_time;
    }
}
