<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Inflector;

/**
 * 基础ActiveRecord
 * @property string $validatorError 验证错误信息
 */
class BaseModel extends ActiveRecord
{
    /**
     * 使用驼峰式命名的数据设置属性
     * @param array $attributes 要赋值的属性
     * @param array $excepts    如果在此参数中则忽略转换直接赋值
     * @return static
     */
    public function setCamelAttributes(array $attributes, array $excepts = []): BaseModel
    {
        $separator = '_';
        $keys = array_keys($attributes);
        $length = count($excepts);
        foreach ($keys as $key) {
            if ($length > 0 && in_array($key, $excepts)) {
                continue;
            }
            $newKey = Inflector::camel2id($key, $separator);
            if (strpos($newKey, $separator) !== false) {
                $attributes[$newKey] = $attributes[$key];
                unset($attributes[$key]);
            }
        }
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * 获取验证错误信息
     * @return mixed|null
     */
    public function getValidatorError()
    {
        $errors = array_values($this->firstErrors);
        if (count($errors)) {
            return $errors[0];
        }
        return null;
    }
}
