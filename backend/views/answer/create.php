<?php

declare(strict_types=1);

use backend\forms\TestingAnswerForm;
use backend\models\AnswerImageUploadForm;
use common\models\StoryTestQuestion;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTestQuestion $question
 * @var TestingAnswerForm $formModel
 * @var AnswerImageUploadForm $answerImageModel
 */

?>
<?php $form = ActiveForm::begin(['id' => 'answer-form']); ?>
<?= $form->field($formModel, 'name')->textarea(['rows' => 5]) ?>
<?= $form->field($answerImageModel, 'answerImage')->fileInput() ?>
<?= $form->field($formModel, 'is_correct')->checkbox() ?>
<div class="form-group">
    <?= Html::submitButton('Создать ответ', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
