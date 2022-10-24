<?php

declare(strict_types=1);

use backend\forms\ContactRequestCommentForm;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var View $this
 * @var ContactRequestCommentForm $formModel
 */
?>
<?php $form = ActiveForm::begin(['id' => 'comment-form']) ?>
    <div class="modal-header">
        <h5 class="modal-title">Комментарий</h5>
        <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <?= $form->field($formModel, 'comment')->textarea(['rows' => 4]); ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
    </div>
<?php ActiveForm::end() ?>
<?php
$this->registerJs(<<<JS
(function() {
    const doneCallback = (response) => {
        if (response && response.success) {
            $('#comment-modal').modal('hide');
            $.pjax.reload({container: '#pjax-contact-requests', async: false});
        }
        else {
            toastr.error(response['error'] || 'Неизвестная ошибка');
        }
    };
    const failCallback = (response) => {
        console.log(response);
        toastr.error(response.responseJSON.message);
    }
    const form = $('#comment-form');
    yiiModalFormInit(form, doneCallback, failCallback);
})();
JS
);
