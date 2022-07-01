<?php

namespace app\models\base;

use app\models\BaseModel;

/**
 * 用户
 * @property int    $id
 * @property string $username  用户名
 * @property string $password  登录密码
 * @property string $name      姓名
 * @property string $avatar    头像
 * @property int    $state     状态
 * @property string $loginTime 上次登录时间
 */
class BaseUser extends BaseModel
{
    /**
     * 表名
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * 设置用户名
     * @param string|null $value 参数值
     */
    public function setUsername(?string $value)
    {
        $this->username = $value;
    }

    /**
     * 获取用户名
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * 设置登录密码
     * @param string|null $value 参数值
     */
    public function setPassword(?string $value)
    {
        $this->password = $value;
    }

    /**
     * 获取登录密码
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * 设置姓名
     * @param string|null $value 参数值
     */
    public function setName(?string $value)
    {
        $this->name = $value;
    }

    /**
     * 获取姓名
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置头像
     * @param string|null $value 参数值
     */
    public function setAvatar(?string $value)
    {
        $this->avatar = $value;
    }

    /**
     * 获取头像
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * 设置状态
     * @param int|null $value 参数值
     */
    public function setState(?int $value)
    {
        $this->state = $value;
    }

    /**
     * 获取状态
     * @return int|null
     */
    public function getState(): ?int
    {
        return $this->state;
    }

    /**
     * 设置上次登录时间
     * @param string|null $value 参数值
     */
    public function setLoginTime(?string $value)
    {
        $this->login_time = $value;
    }

    /**
     * 获取上次登录时间
     * @return string|null
     */
    public function getLoginTime(): ?string
    {
        return $this->login_time;
    }
}
