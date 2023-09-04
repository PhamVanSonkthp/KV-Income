<?php


return [
    'access' => [
        'roles-add' => 'roles_add',
        'roles-list' => 'roles_list',
        'roles-get' => 'roles_get',
        'roles-edit' => 'roles_edit',
        'roles-delete' => 'roles_delete',

        'users-add' => 'users_add',
        'users-list' => 'users_list',
        'users-get' => 'users_get',
        'users-edit' => 'users_edit',
        'users-delete' => 'users_delete',

        'history_datas-list' => 'history_datas_list',
        'history_datas-get' => 'history_datas_get',

        'employees-add' => 'employees_add',
        'employees-list' => 'employees_list',
        'employees-get' => 'employees_get',
        'employees-edit' => 'employees_edit',
        'employees-delete' => 'employees_delete',


        'orders-add' => 'orders_add',
        'orders-list' => 'orders_list',
        'orders-get' => 'orders_get',
        'orders-edit' => 'orders_edit',
        'orders-delete' => 'orders_delete',

        'salarys-list' => 'salaries_list',
        'salarys-get' => 'salaries_get',
    ],
    'table_module'=>[
        'roles',
        'users',
        'history_datas',
        'employees',
        'orders',
        'salaries'
    ],

    'table_module_name'=>[
        'Admin groups',
        'Admin users',
        'Admin Activity logs',
        'Staffs',
        'Orders',
        'Employee salaries'
    ],

    'module_children'=>[
        'add',
        'list',
        'get',
        'edit',
        'delete',
    ]
];
