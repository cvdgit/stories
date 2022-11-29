<?php

declare(strict_types=1);

use backend\forms\WordListForm;
use backend\widgets\SelectStoryWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var WordListForm $model
 * @var ActiveForm $form
 */
?>
<div class="test-word-list-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'story_id')->widget(SelectStoryWidget::class, ['storyModel' => $model->getStory()]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord() ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
