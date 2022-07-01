<?php

namespace app\models\base;

use app\models\BaseModel;

/**
 * 路由
 * @property int $id
 * @property int $permissionId 权限
 * @property string $name 名称
 * @property string $path 路径
 */
class BaseRoute extends BaseModel
{
    /**
     * 表名
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%route}}';
    }

    /**
     * 设置权限
     * @param int|null $value 参数值
     */
    public function setPermissionId(?int $value)
    {
        $this->permission_id = $value;
    }

    /**
     * 获取权限
     * @return int|null
     */
    public function getPermissionId(): ?int    {
        return $this->permission_id;
    }

    /**
     * 设置名称
     * @param string|null $value 参数值
     */
    public function setName(?string $value)
    {
        $this->name = $value;
    }

    /**
     * 获取名称
     * @return string|null
     */
    public function getName(): ?string    {
        return $this->name;
    }

    /**
     * 设置路径
     * @param string|null $value 参数值
     */
    public function setPath(?string $value)
    {
        $this->path = $value;
    }

    /**
     * 获取路径
     * @return string|null
     */
    public function getPath(): ?string    {
        return $this->path;
    }
}
