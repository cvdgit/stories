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
            [
                'label' => 'Youtube',
                'url' => '#',
                'linkOptions' => ['onclick' => "StoryEditor.createBlock('video'); return false;"],
            ],
        ],
    ]
];
?>
<div>
    <h4>Блоки <div class="pull-right"><?= ButtonDropdown::widget($options) ?></div></h4>
    <div>
        <div class="btn-group" data-toggle="buttons">
            <button class="btn btn-default" id="create-image-action" data-toggle="tooltip" title="Изображение">
                <input type="checkbox" checked> <i class="glyphicon glyphicon-picture" style="pointer-events: none;"></i>
            </button>
        </div>
    </div>
    <div class="list-group" id="slide-block-list" style="margin-top: 20px"></div>
</div>
