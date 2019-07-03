<?php

use common\models\News;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use vova07\imperavi\Widget;

/** @var $this yii\web\View */
/** @var $model common\models\News */

?>

<div class="news-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(['id' => 'news-form']) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => 250]) ?>
    <?= $form->field($model, 'slug')->textInput(['maxlength' => 250]) ?>
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
    <?= $form->field($model, 'status')->dropDownList(News::getStatuses()) ?>
    <div class="form-group">
        <?= Html::submitButton('Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end() ?>
</div>