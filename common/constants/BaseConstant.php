<?php

namespace app\common\constants;

class BaseConstant
{
    /**
     * 成员集合
     * @return array
     */
    public static function items(): array
    {
        return [];
    }

    /**
     * 获取值列表
     * @return array
     */
    public static function values(): array
    {
        return array_map(function ($item) {
            return $item['value'];
        }, static::items());
    }

    /**
     * 根据值获取名称
     * @param mixed $value 值
     * @return string|null
     */
    public static function name($value): ?string
    {
        foreach (static::items() as $item) {
            if ($item['value'] == $value) {
                return $item['name'];
            }
        }
        return null;
    }

    /**
     * 根据名称获取值
     * @param mixed $name 名称
     * @return int|null|string
     */
    public static function value($name)
    {
        foreach (static::items() as $item) {
            if ($item['name'] == $name) {
                return $item['value'];
            }
        }
        return null;
    }

    /**
     * 值是否存在
     * @param mixed $value 值
     * @return bool
     */
    public static function exists($value): bool
    {
        return in_array($value, static::values());
    }
}