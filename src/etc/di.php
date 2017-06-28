<?php

return [
    'preference' => [],

    'virtualType' => [
        'Brisum\InventorySynchronization\VisualComponent\StorageFileList' => [
            'type' => 'Brisum\Lib\VisualComponent\VisualComponent',
            'shared' => false,
            'arguments' => [
                'dataProvider' => [
                    'type' => 'object',
                    'value' => 'Brisum\InventorySynchronization\VisualComponent\Storage\DataProvider\FileList'
                ],
                'template' => [
                    'value' => "module/Menu/VisualComponent/MobileMenu/Skin/Default/template.php"
                ]
            ]

        ],
    ],

    'type' => []
];
