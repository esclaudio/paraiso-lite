<?php

return [
    'title' => trans('Documentation'),
    'groups' => [
        [
            [
                'title' => trans('Current Documents'),
                'route' => 'current_documents.index',
            ],
        ],
        [ 
            [
                'title' => trans('Documents'),
                'route' => 'documents.index',
                'permission' => 'documents.show',
            ],
        ],
        [
            [
                'title' => trans('Documents Types'),
                'route' => 'documents_types.index',
                'permission' => 'documents_types.show',
            ],
        ],
    ]
];