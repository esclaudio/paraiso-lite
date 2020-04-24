<?php

return [
    'title' => trans('Settings'),
    'groups' => [
        [
            [
                'title' => trans('Systems'),
                'route' => 'systems.index',
                'permission' => 'systems.show'
            ],
            [
                'title' => trans('Processes'),
                'route' => 'processes.index',
                'permission' => 'processes.show'
            ],
        ],
        [
            [
                'title' => trans('Customers'),
                'route' => 'customers.index',
                'permission' => 'customers.show'
            ],
            [
                'title' => trans('Products/Services'),
                'route' => 'products.index',
                'permission' => 'products.view'
            ],
        ],
        [
            [
                'title' => trans('Roles'),
                'route' => 'roles.index',
                'permission' => 'roles.show'
            ],
            [
                'title' => trans('Users'),
                'route' => 'users.index',
                'permission' => 'users.show'
            ],
        ],
    ],
];