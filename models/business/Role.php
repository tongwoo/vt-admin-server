<?php

namespace app\models\business;

use app\models\base\BaseRole;
use Yii;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\AttributeTypecastBehavior;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use app\common\constants\Confirm;

/**
 * 角色
 * @property Permission[]|ActiveQuery $permissions     角色权限列表
 * @property User[]|ActiveQuery       $users           角色用户列表
 * @property string                   $isBuiltInName   是否内置名称
 */
class Role extends BaseRole
{
    /**
     * 规则
     * @return array
     */
    public function rules(): array
    {
        return [
            //角色名称
            [
                'name',
                'required'
            ],
            [
                'name',
                'string',
                'strict' => false,
                'max' => 32
            ],
            [
                'name',
                'unique'
            ],
            //角色描述
            [
                'description',
                'required'
            ],
            [
                'description',
                'string',
                'strict' => false,
                'max' => 32
            ],
            //规则名称
            [
                'rule_name',
                'default',
                'value' => ''
            ],
            [
                'rule_name',
                'string',
                'strict' => false,
                'max' => 50
            ],
            //是否内置
            [
                'is_built_in',
                'default',
                'value' => 0
            ],
            [
                'is_built_in',
                'integer',
                'min' => 0,
                'max' => 255
            ],
            [
                'is_built_in',
                'in',
                'range' => Confirm::values()
                //'range' => Confirm::values()
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
            //角色名称
            'name',
            //角色描述
            'description',
            //规则名称
            'ruleName' => 'rule_name',
            //是否内置
            'isBuiltIn' => 'is_built_in',
            //是否内置名称
            'isBuiltInName' => 'isBuiltInName',
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => '角色名称',
            'description' => '角色描述',
            'rule_name' => '规则名称',
            'is_built_in' => '是否内置',
        ];
    }

    /**
     * 角色权限列表
     * @return ActiveQuery
     */
    public function getPermissions(): ActiveQuery
    {
        return $this->hasMany(Permission::class, ['id' => 'permission_id'])
            ->viaTable('role_permission', ['role_id' => 'id']);
    }

    /**
     * 角色用户列表
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('user_role', ['role_id' => 'id']);
    }

    /**
     * 获取是否内置名称
     * @return string|null
     */
    public function getIsBuiltInName(): ?string
    {
        return Confirm::name($this->isBuiltIn);
    }

    /**
     * 创建/取出同配置的RBAC角色
     * @return \yii\rbac\Role
     */
    public function getRbacRole(): \yii\rbac\Role
    {
        $manager = Yii::$app->authManager;
        $rbac = $manager->getRole($this->name);
        if ($rbac === null) {
            $rbac = new \yii\rbac\Role();
        }
        $rbac->name = $this->name;
        $rbac->description = $this->description;
        $rbac->ruleName = $this->ruleName;
        return $rbac;
    }

}
