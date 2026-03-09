<?php

declare(strict_types=1);

use backend\widgets\SelectStoryWidget;
use backend\widgets\SelectUserWidget;
use modules\edu\models\EduStory;
use modules\edu\models\EduStudent;
use modules\edu\RequiredStory\Edit\RequiredStoryEditForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var RequiredStoryEditForm $formModel
 * @var array $students
 * @var EduStory|null $storyModel
 * @var EduStudent|null $studentModel
 */

$this->registerJs('window.requiredStoryMetadata = ' . $formModel->metadata, View::POS_HEAD);
?>
<div style="text-align: initial">
    <?php
    $form = ActiveForm::begin([
        'action' => ['/edu/teacher/required-story/edit-handler'],
        'id' => 'required-story-edit-form',
    ]); ?>
    <?= $this->render('_form', [
        'form' => $form,
        'formModel' => $formModel,
        'userModel' => $studentModel,
        'storyModel' => $storyModel,
    ]) ?>
    <div style="padding: 20px 0">
        <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
        <button type="submit" class="btn btn-small">Сохранить</button>
    </div>
    <?php
    ActiveForm::end(); ?>
</div>
