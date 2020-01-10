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
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>История</th>
                    <th>Слайд</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $model->getModel()->slide->story->title ?></td>
                    <td><?= $model->getModel()->slide->number ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <div style="margin-top: 72px">
            <?= Html::img(Yii::$app->urlManagerFrontend->createAbsoluteUrl(['image/view', 'id' => $model->getModel()->hash]), ['width' => 500]) ?>
        </div>
    </div>
</div>

