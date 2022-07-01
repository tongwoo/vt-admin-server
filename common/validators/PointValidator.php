<?php

namespace app\common\validators;

use yii\validators\Validator;

/**
 * 坐标点验证器
 */
class PointValidator extends Validator
{
    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        $point = $model->$attribute;
        if (!is_array($point)) {
            $message = $this->message ?: '{attribute} 必须是一个数组';
            $this->addError($model, $attribute, $message);
            return;
        }
        if (count($point) !== 2) {
            $message = $this->message ?: '{attribute} 必须是一个有效的数组';
            $this->addError($model, $attribute, $message);
            return;
        }
        if (!is_numeric($point[0]) || !is_numeric($point[1])) {
            $message = $this->message ?: '{attribute} 必须是一个有效的数组';
            $this->addError($model, $attribute, $message);
        }
        if (
            bccomp($point[0], -180, 6) === -1 ||
            bccomp($point[0], 180, 6) === 1 ||
            bccomp($point[1], -90, 6) === -1 ||
            bccomp($point[1], 90, 6) === 1
        ) {
            $message = $this->message ?: '{attribute} 范围无效';
            $this->addError($model, $attribute, $message);
        }
    }
}