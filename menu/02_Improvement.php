<?php

return [
    'title' => trans('Improvement'),
    'groups' => [
        [
            [
                'title' => trans('Non Conformities'),
                'route' => 'nonconformities.index',
                'permission' => 'nonconformities.view'
            ],
        ],
    ]
];