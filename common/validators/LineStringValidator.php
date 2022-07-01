<?php

namespace app\common\validators;

use yii\validators\Validator;

/**
 * 线验证器
 */
class LineStringValidator extends Validator
{
    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        $points = $model->$attribute;
        if (!is_array($points)) {
            $message = $this->message ?: '{attribute} 必须是一个数组';
            $this->addError($model, $attribute, $message);
            return;
        }
        if (count($points) < 2) {
            $message = $this->message ?: '{attribute} 必须是一个有效的数组';
            $this->addError($model, $attribute, $message);
            return;
        }
        foreach ($points as $i => $point) {
            if (!is_numeric($point[0]) || !is_numeric($point[1])) {
                $message = $this->message ?: '{attribute} 第 ' . ($i + 1) . ' 个坐标点必须是一个有效的数组';
                $this->addError($model, $attribute, $message);
            }
            if (
                bccomp($point[0], -180, 6) === -1 ||
                bccomp($point[0], 180, 6) === 1 ||
                bccomp($point[1], -90, 6) === -1 ||
                bccomp($point[1], 90, 6) === 1
            ) {
                $message = $this->message ?: '{attribute} 第 ' . ($i + 1) . ' 个坐标点范围无效';
                $this->addError($model, $attribute, $message);
            }
        }
    }
}