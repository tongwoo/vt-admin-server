<?php

namespace app\common\constants;

/**
 * 状态
 */
class UserState extends BaseConstant
{
    /**
     * 启用
     */
    const ENABLED = 1;

    /**
     * 禁用
     */
    const DISABLED = 0;

    /**
     * 获取状态列表
     * @return array
     */
    public static function items(): array
    {
        return [
            [
                'name' => '启用',
                'value' => static::ENABLED
            ],
            [
                'name' => '禁用',
                'value' => static::DISABLED
            ],
        ];
    }
}