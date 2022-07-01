<?php

namespace app\models\business;

use app\models\base\BaseAuthorization;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use app\common\constants\Confirm;

/**
 * 用户授权
 * @property User $user 用户
 */
class Authorization extends BaseAuthorization
{
    /**
     * 规则
     * @return array
     */
    public function rules(): array
    {
        return [
            //用户
            [
                'user_id',
                'required'
            ],
            [
                'user_id',
                'integer',
                'min' => 0,
                'max' => 4294967295
            ],
            [
                'user_id',
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'id'
            ],
            //授权值
            [
                'value',
                'required'
            ],
            [
                'value',
                'string',
                'strict' => false,
                'max' => 128
            ],
            //过期时间
            [
                'expires_time',
                'required'
            ],
            [
                'expires_time',
                'date',
                'format' => 'php:Y-m-d H:i:s'
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
            //用户
            'userId' => 'user_id',
            //用户名称
            'userName' => function () {
                return $this->user ? $this->user->name : null;
            },
            //授权值
            'value',
            //过期时间
            'expiresTime' => 'expires_time',
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'user_id' => '用户',
            'value' => '授权值',
            'expires_time' => '过期时间',
        ];
    }

    /**
     * 用户
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
