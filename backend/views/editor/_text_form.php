<?php
/** @var $form yii\widgets\ActiveForm */
use vova07\imperavi\Widget;
$form->action = ['update-text'];
/** @var $model backend\models\editor\TextForm */
//echo $form->field($model, 'text_size', ['inputOptions' => ['class' => 'form-control input-sm']])->textInput();
echo $form->field($model, 'text', ['inputOptions' => ['class' => 'form-control input-sm']])->widget(Widget::class, [
    'settings' => [
        'lang' => 'ru',
        'minHeight' => 200,
        'buttons' => ['html', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'alignment', 'horizontalrule'],
        'plugins' => [
            'fontcolor',
            'fontsize',
            'fontfamily',
        ],
    ],
]);
