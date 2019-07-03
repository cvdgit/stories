<?php

use vova07\imperavi\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\News $model
 * @var ActiveForm $form
 */

$this->title = 'Новая публикация';
?>
<div class="row">
    <div class="col-xs-12">
        <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
        <div class="news-add">
            <?php $form = ActiveForm::begin(['id' => 'news-add']) ?>
            <?= $form->field($model, 'title')->textInput(['maxlength' => 250]) ?>
            <?= $form->field($model, 'text')->widget(Widget::class, [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 200,
                    'imageUpload' => Url::to(['/news/image-upload']),
                    'plugins' => [
                        'clips',
                        'fullscreen',
                        'imagemanager',
                    ],
                    'clips' => [
                        ['Lorem ipsum...', 'Lorem...'],
                        ['red', '<span class="label-red">red</span>'],
                        ['green', '<span class="label-green">green</span>'],
                        ['blue', '<span class="label-blue">blue</span>'],
                    ],
                ],
            ]) ?>
            <div class="form-group">
                <?= Html::submitButton('Создать запись', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
