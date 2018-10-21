<?php
return [
    'createStory' => [
        'type' => 2,
        'description' => 'Создать историю',
    ],
    'updateStory' => [
        'type' => 2,
        'description' => 'Изменить историю',
    ],
    'deleteStory' => [
        'type' => 2,
        'description' => 'Удалить историю',
    ],
    'author' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'createStory',
            'updateStory',
            'deleteStory',
            'updateOwnStory',
        ],
    ],
    'admin' => [
        'type' => 1,
        'ruleName' => 'userGroup',
        'children' => [
            'author',
        ],
    ],
    'updateOwnStory' => [
        'type' => 2,
        'description' => 'Редактировать свою историю',
        'ruleName' => 'isAuthor',
        'children' => [
            'updateStory',
        ],
    ],
];
