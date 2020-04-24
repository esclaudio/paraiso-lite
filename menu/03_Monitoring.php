<?php

return [
    'title' => trans('Monitoring'),
    'groups' => [
        [
            [
                'title' => trans('Indicators'),
                'route' => 'indicators.index',
                'permission' => 'indicators.show'
            ],
        ],
    ],
];