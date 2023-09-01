<?php

return [
    'admin' => [
        'type' => 1,
        'description' => '管理员',
        'children' => [
            'route',
            'route_read',
            'sdgdf',
            'sdgdf_read',
            'sdgdf_update',
        ],
    ],
    'model-builder' => [
        'type' => 1,
        'description' => '建模师',
        'ruleName' => 'role-rule',
        'children' => [
            'user',
            'user_create',
            'user_read',
            'user_update',
            'user_delete',
            'permission',
            'permission_create',
            'permission_read',
            'permission_update',
            'permission_delete',
        ],
    ],
    'gm' => [
        'type' => 1,
        'description' => '游戏管理员',
        'children' => [
            'aaaaaaaaaaaa',
            'vvvvvvvv',
        ],
    ],
    'service' => [
        'type' => 1,
        'description' => '客服',
        'children' => [
            'route',
            'route_read',
            'sdgdf',
            'sdgdf_create',
            'sdgdf_read',
            'sdgdf_update',
            'sdgdf_delete',
        ],
    ],
    'saler' => [
        'type' => 1,
        'description' => '销售',
        'ruleName' => 'role-rule',
        'children' => [
            'aaaaaaaaaaaa',
            'vvvvvvvv',
        ],
    ],
    'user_create' => [
        'type' => 2,
        'description' => '创建用户',
    ],
    'user_read' => [
        'type' => 2,
        'description' => '查看用户',
    ],
    'user_update' => [
        'type' => 2,
        'description' => '修改用户',
    ],
    'user_delete' => [
        'type' => 2,
        'description' => '删除用户',
    ],
    'role' => [
        'type' => 2,
        'description' => '角色管理',
    ],
    'role_read' => [
        'type' => 2,
        'description' => '查看角色',
    ],
    'role_update' => [
        'type' => 2,
        'description' => '修改角色',
    ],
    'role_delete' => [
        'type' => 2,
        'description' => '删除角色',
    ],
    'user' => [
        'type' => 2,
        'description' => '用户管理',
    ],
    'permission' => [
        'type' => 2,
        'description' => '权限管理',
    ],
    'permission_create' => [
        'type' => 2,
        'description' => '创建权限',
    ],
    'permission_read' => [
        'type' => 2,
        'description' => '查看权限',
    ],
    'permission_update' => [
        'type' => 2,
        'description' => '修改权限',
    ],
    'permission_delete' => [
        'type' => 2,
        'description' => '删除权限',
    ],
    'route' => [
        'type' => 2,
        'description' => '路由管理',
    ],
    'route_create' => [
        'type' => 2,
        'description' => '创建路由',
    ],
    'route_read' => [
        'type' => 2,
        'description' => '查看路由',
    ],
    'route_update' => [
        'type' => 2,
        'description' => '修改路由',
    ],
    'route_delete' => [
        'type' => 2,
        'description' => '删除路由',
    ],
    'sdgdf' => [
        'type' => 2,
        'description' => 'fgf',
    ],
    'sdgdf_create' => [
        'type' => 2,
        'description' => '创建fgf',
    ],
    'sdgdf_read' => [
        'type' => 2,
        'description' => '查看fgf',
    ],
    'sdgdf_update' => [
        'type' => 2,
        'description' => '修改fgf',
    ],
    'sdgdf_delete' => [
        'type' => 2,
        'description' => '删除fgf',
    ],
    'aaaaaaaaaaaa' => [
        'type' => 2,
        'description' => 'ddddddddddd',
    ],
    'vvvvvvvv' => [
        'type' => 2,
        'description' => 'ccccccc',
    ],
    '胜多负少的' => [
        'type' => 2,
        'description' => '似懂非懂',
    ],
];
