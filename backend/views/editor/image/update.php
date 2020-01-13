<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model backend\models\ImageForm */

$this->title = 'Изображение';
?>
<div class="row">
    <div class="col-md-6">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'source_url')->textInput(['readonly' => true]) ?>
        <?= $form->field($model, 'collection_name')->textInput(['readonly' => true]) ?>
        <?php ActiveForm::end(); ?>
        <h4>Изображение в историях</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>История</th>
                    <th>Слайд</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->getModel()->slides as $slide): ?>
                <tr>
                    <td><?= $slide->story->title ?></td>
                    <td><?= $slide->number ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <h4>Связанные изображения</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Изображение</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($model->getModel()->linkImages as $image): ?>
                <tr>
                    <td><?= $image->source_url ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <div style="margin-top: 72px">
            <?= Html::img(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $model->getModel()->hash]), ['width' => 500]) ?>
        </div>
    </div>
</div>

