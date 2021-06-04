<?php
use vova07\imperavi\Widget;
/** @var $form yii\widgets\ActiveForm */
/** @var $model backend\models\editor\TextForm */
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
