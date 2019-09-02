<?php

use yii\bootstrap\ButtonDropdown;

$options = [
    'encodeLabel' => false,
    'label' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
    'options' => [
        'class' => 'btn-sm btn-default',
        'title' => 'Добавить блок',
    ],
    'dropdown' => [
        'items' => [
            [
                'label' => 'Кнопка',
                'url' => '#',
                'linkOptions' => ['onclick' => 'StoryEditor.newCreateBlock(); return false;'],
            ],
            [
                'label' => 'Переход к истории',
                'url' => '#',
                'linkOptions' => ['onclick' => "StoryEditor.createBlock('transition'); return false;"],
            ],
            [
                'label' => 'Текст',
                'url' => '#',
                'linkOptions' => ['onclick' => "StoryEditor.createBlock('text'); return false;"],
            ],
            [
                'label' => 'Тест',
                'url' => '#',
                'linkOptions' => ['onclick' => "StoryEditor.createBlock('test'); return false;"],
            ],
        ],
    ]
];
?>
<div>
    <h4>Блоки <div class="pull-right"><?= ButtonDropdown::widget($options) ?></div></h4>
    <div class="list-group" id="slide-block-list" style="margin-top: 20px"></div>
</div>
