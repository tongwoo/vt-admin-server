<?php

namespace app\forms;

use yii\base\Model;

/**
 * 用户表单
 */
class UserForm extends Model
{
    /**
     * 用户名
     */
    public $username;

    /**
     * 密码
     */
    public $password;


    /**
     * 姓名
     */
    public $name;

    /**
     * 角色集合
     */
    public ?array $roleIds = null;

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'username' => '用户名',
            'password' => '用户名',
            'name' => '姓名',
            'roleIds' => '角色',
        ];
    }


    /**
     * @inheritDoc
     */
    public function rules(): array
    {
        return [
            [
                'username',
                'required'
            ],
            [
                'password',
                'required',
                'on' => 'create'
            ],
            [
                'name',
                'required'
            ],
            [
                'roleIds',
                'required'
            ],
            [
                'roleIds',
                'each',
                'rule' => [
                    'integer'
                ]
            ],
        ];
    }
}
