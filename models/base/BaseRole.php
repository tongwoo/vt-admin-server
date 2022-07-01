<?php

namespace app\models\base;

use app\models\BaseModel;

/**
 * 角色
 * @property int    $id
 * @property string $name        角色名称
 * @property string $description 角色描述
 * @property string $ruleName    规则名称
 * @property int    $isBuiltIn   是否内置
 */
class BaseRole extends BaseModel
{
    /**
     * 表名
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%role}}';
    }

    /**
     * 设置角色名称
     * @param string|null $value 参数值
     */
    public function setName(?string $value)
    {
        $this->name = $value;
    }

    /**
     * 获取角色名称
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 设置角色描述
     * @param string|null $value 参数值
     */
    public function setDescription(?string $value)
    {
        $this->description = $value;
    }

    /**
     * 获取角色描述
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

    /**
     * 设置是否内置
     * @param int|null $value 参数值
     */
    public function setIsBuiltIn(?int $value)
    {
        $this->is_built_in = $value;
    }

    /**
     * 获取是否内置
     * @return int|null
     */
    public function getIsBuiltIn(): ?int
    {
        return $this->is_built_in;
    }
}
