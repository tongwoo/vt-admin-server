<?php
$rbacItems = require_once __DIR__.'/../rbac/items.php';
$rbacRoleNames = [];
foreach ($rbacItems as $name => $rbacItem) {
    if ($rbacItem['type'] === 1) {
        $rbacRoleNames[] = $name;
    }
}

return [
    'class' => 'yii\rbac\PhpManager',
    'defaultRoles' => $rbacRoleNames
];
