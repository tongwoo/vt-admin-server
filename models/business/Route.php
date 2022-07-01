<?php

namespace app\models\business;

use app\models\base\BaseRoute;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use app\common\constants\Confirm;

/**
 * 路由
 * @property Permission $permission 权限
 */
class Route extends BaseRoute
{
    /**
     * 规则
     * @return array
     */
    public function rules(): array
    {
        return [
            //权限
            [
                'permission_id',
                'integer',
                'min' => 0,
                'max' => 4294967295
            ],
            [
                'permission_id',
                'exist',
                'targetClass' => Permission::class,
                'targetAttribute' => 'id'
            ],
            //名称
            [
                'name',
                'string',
                'strict' => false,
                'max' => 128
            ],
            //路径
            [
                'path',
                'string',
                'strict' => false,
                'max' => 256
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
            //权限
            'permissionId' => 'permission_id',
            //权限名称
            'permissionName' => function () {
                return $this->permission ? $this->permission->name : null;
            },
            //权限描述
            'permissionDescription' => function () {
                return $this->permission ? $this->permission->description : null;
            },
            //名称
            'name',
            //路径
            'path',
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'permission_id' => '权限',
            'name' => '名称',
            'path' => '路径',
        ];
    }

    /**
     * 权限
     * @return ActiveQuery
     */
    public function getPermission(): ActiveQuery
    {
        return $this->hasOne(Permission::class, ['id' => 'permission_id']);
    }

}
