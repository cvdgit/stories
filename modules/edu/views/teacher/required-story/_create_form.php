<?php

declare(strict_types=1);

use backend\widgets\SelectStoryWidget;
use backend\widgets\SelectUserWidget;
use modules\edu\RequiredStory\Create\RequiredStoryCreateForm;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var RequiredStoryCreateForm $formModel
 * @var array $students
 */
?>
<div style="text-align: initial">
    <?php
    $form = ActiveForm::begin([
        'action' => ['/edu/teacher/required-story/create-handler'],
        'id' => 'required-story-create-form',
    ]); ?>
    <?= $this->render('_form', [
        'form' => $form,
        'formModel' => $formModel,
        'userModel' => null,
        'storyModel' => null,
        'students' => $students,
    ]) ?>
    <div style="padding: 20px 0">
        <button type="submit" class="btn btn-small">Создать</button>
    </div>
    <?php
    ActiveForm::end(); ?>
</div>
