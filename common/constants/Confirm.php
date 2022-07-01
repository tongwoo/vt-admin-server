<?php

namespace app\common\constants;

/**
 * 是否
 */
class Confirm extends BaseConstant
{
    /**
     * 是
     */
    const YES = 1;

    /**
     * 否
     */
    const NO = 0;

    /**
     * @return array
     */
    public static function items(): array
    {
        return [
            [
                'name' => '是',
                'value' => static::YES
            ],
            [
                'name' => '否',
                'value' => static::NO
            ],
        ];
    }
}