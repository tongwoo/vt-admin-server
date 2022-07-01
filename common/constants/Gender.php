<?php

namespace app\common\constants;

/**
 * 性别
 */
class Gender extends BaseConstant
{
    /**
     * 男
     */
    const MALE = 1;

    /**
     * 女
     */
    const FEMALE = 0;

    /**
     * 获取性别列表
     * @return array
     */
    public static function items(): array
    {
        return [
            [
                'name' => '男',
                'value' => static::MALE
            ],
            [
                'name' => '女',
                'value' => static::FEMALE
            ],
        ];
    }
}