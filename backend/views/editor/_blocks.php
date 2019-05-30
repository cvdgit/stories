<?php

use yii\bootstrap\ButtonDropdown;

/** @var $model backend\models\SlideEditorForm */

?>

<div>
    <?php $options = [
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
                    'linkOptions' => ['onclick' => "StoryEditor.createBlock('button'); return false;"],
                ],
                [
                    'label' => 'Переход к истории',
                    'url' => '#',
                    'linkOptions' => ['onclick' => "StoryEditor.createBlock('transition'); return false;"],
                ],
            ],
        ]
    ]; ?>
    <h4>Блоки</h4>
    <div class="list-group" id="slide-block-list"></div>
    <div class="clearfix" style="margin: 10px 0">
        <div class="pull-right"><?= ButtonDropdown::widget($options) ?></div>
    </div>
</div>
