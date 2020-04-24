<?php

return [
    'title' => trans('Documentation'),
    'groups' => [
        [
            [
                'title' => trans('Current documents'),
                'route' => 'current_documents.index',
            ],
            [
                'title' => trans('Pending documents'),
                'route' => 'pending_documents.index',
            ],
        ],
        [ 
            [
                'title' => trans('Documents'),
                'route' => 'documents.index',
                'permission' => 'document.show',
            ],
        ],
        [
            [
                'title' => trans('Documents types'),
                'route' => 'documents_types.index',
                'permission' => 'documents_types.show',
            ],
        ],
    ]
];