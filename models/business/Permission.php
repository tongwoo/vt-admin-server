<?php

namespace app\models\business;

use app\models\base\BasePermission;
use Yii;

/**
 * 权限
 * @property Permission[] $children 子权限列表
 */
class Permission extends BasePermission
{
    /**
     * 规则
     * @return array
     */
    public function rules(): array
    {
        return [
            //父权限
            [
                'parent_id',
                'required'
            ],
            [
                'parent_id',
                'integer',
                'min' => 0,
                'max' => 4294967295
            ],
            [
                'parent_id',
                function ($attribute) {
                    if ($this->$attribute === $this->id) {
                        $this->addError($attribute, '父权限不能是自己');
                    }
                }
            ],
            //权限名称
            [
                'name',
                'default',
                'value' => ''
            ],
            [
                'name',
                'string',
                'strict' => false,
                'max' => 32
            ],
            //权限描述
            [
                'description',
                'default',
                'value' => ''
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
            [
                'rule_name',
                function ($attribute) {
                    $rule = Yii::$app->authManager->getRule($this->$attribute);
                    if (!$rule) {
                        $this->addError($attribute, '规则不存在');
                    }
                }
            ]
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
            //父权限
            'parentId' => 'parent_id',
            //权限名称
            'name',
            //权限描述
            'description',
            //规则名称
            'ruleName' => 'rule_name',
        ];
    }

    /**
     * 属性标签
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'parent_id' => '父权限',
            'name' => '权限名称',
            'description' => '权限描述',
            'rule_name' => '规则名称',
        ];
    }

    /**
     * 获得子权限列表
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Permission::class, ['parent_id' => 'id']);
    }

    /**
     * 获得RBAC权限
     * @return \yii\rbac\Permission
     */
    public function getRbacPermission(): \yii\rbac\Permission
    {
        $manager = Yii::$app->authManager;
        $rbacPermission = $manager->getPermission($this->name);
        if (!$rbacPermission) {
            $rbacPermission = new \yii\rbac\Permission();
        }
        $rbacPermission->name = $this->name;
        $rbacPermission->description = $this->description;
        $rbacPermission->ruleName = $this->ruleName;
        return $rbacPermission;
    }

    /**
     * 权限列表转换成树结构
     * @param Permission[] $permissions 权限列表
     */
    public static function listToTree(array $permissions, int $parentId = 0): array
    {
        $items = [];
        foreach ($permissions as $permission) {
            if ($permission->parentId === $parentId) {
                $item = $permission->toArray();
                $item['children'] = self::listToTree($permissions, $permission->id);
                $items[] = $item;
            }
        }
        return $items;
    }
}
