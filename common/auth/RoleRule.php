<?php

namespace app\common\auth;

use app\models\business\Role;
use app\models\business\User;
use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * 默认角色的规则
 */
class RoleRule extends Rule
{
    public $name = 'role-rule';

    /**
     * @inheritDoc
     */
    public function execute($user, $item, $params): bool
    {
        $roles = Role::find()->select(['name'])->asArray()->column();
        /** @var User|null $identity */
        $identity = Yii::$app->user->identity;
        if (!$identity) {
            $userRoles = $identity->getRoles()->select(['name'])->column();
            foreach ($userRoles as $userRole) {
                if (in_array($userRole, $roles)) {
                    return true;
                }
            }
        }
        return false;
    }

}
