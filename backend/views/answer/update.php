<?php

declare(strict_types=1);

use backend\forms\TestingAnswerForm;
use backend\models\AnswerImageUploadForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var TestingAnswerForm $formModel
 * @var AnswerImageUploadForm $answerImageModel
 */

?>
<?php $form = ActiveForm::begin(['id' => 'answer-form']); ?>
<?= $form->field($formModel, 'name')->textarea(['rows' => 5]) ?>
<?= $form->field($answerImageModel, 'answerImage')->fileInput() ?>
<?php if ($formModel->haveImage()): ?>
    <div style="padding: 20px 0; text-align: center">
        <?= Html::img($formModel->getImagePath(), ['style' => 'max-width: 330px']) ?>
        <div>
            <?= Html::a('Удалить изображение', ['/answer/delete-image', 'id' => $formModel->getId()], ['id' => 'delete-image', 'onclick' => "return confirm('Подтверждаете удаление?')"]) ?>
        </div>
    </div>
<?php endif ?>
<?= $form->field($formModel, 'is_correct')->checkbox() ?>
<div class="form-group">
    <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs(<<<JS
(function() {
    $('#delete-image').on('click', function(e) {
        e.preventDefault();
        const elem = $(this);
        $.post(elem.attr('href'))
            .done((response) => {
                elem.parent().parent().remove();
            })
            .fail((response) => {
                toastr.error(response.responseText);
            });
    });
})();
JS
);
