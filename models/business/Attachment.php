<?php

namespace app\models\business;

use app\models\base\BaseAttachment;
use app\common\constants\Confirm;

/**
 * 附件
 * @property string $isValidName 是否有效名称
 */
class Attachment extends BaseAttachment
{
    /**
     * 规则
     * @return array
     */
    public function rules(): array
    {
        return [
            //名称
            [
                'name',
                'required'
            ],
            [
                'name',
                'string',
                'strict' => false,
                'max' => 256
            ],
            //文件名
            [
                'filename',
                'required'
            ],
            [
                'filename',
                'string',
                'strict' => false,
                'max' => 256
            ],
            //扩展名
            [
                'extension',
                'required'
            ],
            [
                'extension',
                'string',
                'strict' => false,
                'max' => 32
            ],
            //路径
            [
                'path',
                'required'
            ],
            [
                'path',
                'string',
                'strict' => false,
                'max' => 512
            ],
            //大小
            [
                'size',
                'required'
            ],
            [
                'size',
                'integer',
                'min' => 0,
                'max' => 4294967295
            ],
            //时间
            [
                'time',
                'required'
            ],
            [
                'time',
                'date',
                'format' => 'php:Y-m-d H:i:s'
            ],
            //哈希
            [
                'hash',
                'string',
                'strict' => false,
                'max' => 32
            ],
            //是否有效
            [
                'is_valid',
                'default',
                'value' => 0
            ],
            [
                'is_valid',
                'integer',
                'min' => 0,
                'max' => 255
            ],
            [
                'is_valid',
                'in',
                'range' => Confirm::values()
            ],
        ];
    }

    /**
     * 字段
     * @return array
     */
    public function fields(): array
    {
        return [
            //主键ID
            'id',
            //名称
            'name',
            //文件名
            'filename',
            //扩展名
            'extension',
            //路径
            'path',
            //大小
            'size',
            //时间
            'time',
            //哈希
            'hash',
            //是否有效
            'isValid' => 'is_valid',
            //是否有效名称
            'isValidName' => 'isValidName',
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => '名称',
            'filename' => '文件名',
            'extension' => '扩展名',
            'path' => '路径',
            'size' => '大小',
            'time' => '时间',
            'hash' => '哈希',
            'is_valid' => '是否有效',
        ];
    }

    /**
     * 获取是否有效名称
     * @return string|null
     */
    public function getIsValidName(): ?string
    {
        return Confirm::name($this->isValid);
    }

}
