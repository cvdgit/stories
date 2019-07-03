<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\News $model
 * @var ActiveForm $form
 */

?>

<?php $this->beginBlock('header'); ?>
<div class="header-title">
    <div class="container">
        <h4>Suggest news</h4>
    </div>
</div>
<?php $this->endBlock(); ?>

<div class="news-add">

    <?php $form = ActiveForm::begin(['id' => 'news-add']) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 250]) ?>

    <?= $form->field($model, 'text')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end() ?>

</div>