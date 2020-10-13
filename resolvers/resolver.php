<?php

return [
    'Query' => [
        'getBooks' => function ($root, $args, $context) {
            return [
                ['id' => 1, 'title' => 'PHP 8']
            ];
        },
        'getAuthors' => function ($root, $args, $context) {
            return [
                ['id' => 1, 'name' => 'Amir Etemad']
            ];
        }
    ]
];