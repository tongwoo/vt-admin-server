<?php

namespace app\models\base;

use app\models\BaseModel;

/**
 * 权限
 * @property int    $id
 * @property int    $parentId    父权限
 * @property string $name        权限名称
 * @property string $description 权限描述
 * @property string $ruleName    规则名称
 */
class BasePermission extends BaseModel
{
    /**
     * 表名
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%permission}}';
    }

    /**
     * 设置父权限
     * @param int|null $value 参数值
     */
    public function setParentId(?int $value)
    {
        $this->parent_id = $value;
    }

    /**
     * 获取父权限
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * 设置权限名称
     * @param string|null $value 参数值
     */
    public function setName(?string $value)
    {
        $this->name = $value;
    }

    /**
     * 获取权限名称
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置权限描述
     * @param string|null $value 参数值
     */
    public function setDescription(?string $value)
    {
        $this->description = $value;
    }

    /**
     * 获取权限描述
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * 设置规则名称
     * @param string|null $value 参数值
     */
    public function setRuleName(?string $value)
    {
        $this->rule_name = $value;
    }

    /**
     * 获取规则名称
     * @return string|null
     */
    public function getRuleName(): ?string
    {
        return $this->rule_name;
    }
}
