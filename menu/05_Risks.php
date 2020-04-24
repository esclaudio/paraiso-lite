<?php

return [
    'title' => trans('Risks'),
    'groups' => [
        [
            [
                'title' => trans('Risks types'),
                'route' => 'risks_types.index',
                'permission' => 'risks_types.show'
            ],
        ],
    ],
];