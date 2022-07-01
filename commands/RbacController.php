<?php

namespace app\commands;

use app\common\auth\RoleRule;
use app\models\business\Permission;
use app\models\business\Role;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * RBAC相关
 */
class RbacController extends Controller
{
    /**
     * 清空RBAC所有数据
     * @return int
     */
    public function actionClear(): int
    {
        $manager = Yii::$app->authManager;
        try {
            $manager->removeAll();
        } catch (Exception $e) {
            $this->stderr($e->getMessage());
        }
        $this->stdout('清除完毕');
        return ExitCode::OK;
    }

    /**
     * 初始化规则
     */
    public function actionInitRules(): int
    {
        $manager = Yii::$app->authManager;
        //角色规则
        $roleRule = new RoleRule();

        $manager->add($roleRule);
        $this->stdout('初始化完毕');
        return ExitCode::OK;
    }

    /**
     * 同步系统数据到RBAC
     * @return int
     */
    public function actionSync(): int
    {
        $manager = Yii::$app->authManager;
        try {
            //$manager->removeAll();
            //所有角色
            $roles = Role::find()->all();
            foreach ($roles as $role) {
                $roleItem = $manager->getRole($role->name);
                $exists = true;
                if (!$roleItem) {
                    $exists = false;
                    $roleItem = new \yii\rbac\Role();
                }
                $roleItem->name = $role->name;
                $roleItem->description = $role->description;
                if ($role->ruleName !== '') {
                    $roleItem->ruleName = $role->ruleName;
                }
                if ($exists) {
                    $manager->update($role->name, $roleItem);
                } else {
                    $manager->add($roleItem);
                }
            }
            $this->stdout('角色处理完成' . "\n");
            //所有权限
            $permissions = Permission::find()->all();
            foreach ($permissions as $permission) {
                $permissionItem = $manager->getPermission($permission->name);
                $exists = true;
                if (!$permissionItem) {
                    $exists = false;
                    $permissionItem = new \yii\rbac\Permission();
                }
                $permissionItem->name = $permission->name;
                $permissionItem->description = $permission->description;
                if ($permission->ruleName !== '') {
                    $permissionItem->ruleName = $permission->ruleName;
                }
                if ($exists) {
                    $manager->update($permission->name, $permissionItem);
                } else {
                    $manager->add($permissionItem);
                }
            }
            $this->stdout('权限处理完成' . "\n");
            //角色权限
            foreach ($roles as $role) {
                foreach ($role->permissions as $permission) {
                    $role = $manager->getRole($role->name);
                    $permission = $manager->getPermission($permission->name);
                    $manager->addChild($role, $permission);
                }
            }
            $this->stdout('角色权限处理完成' . "\n");

        } catch (Exception $e) {
            $this->stderr($e->getMessage());
            return ExitCode::DATAERR;
        }
        $this->stdout('处理完毕' . "\n");
        return ExitCode::OK;
    }
}
