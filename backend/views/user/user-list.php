<?php
use backend\widgets\SelectUserWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $model backend\models\user\SelectUserForm */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Выбор пользователя</h4>
</div>
<?php $form = ActiveForm::begin(['id' => 'select-user-form']) ?>
<div class="modal-body">
    <?= $form->field($model, 'user_id')->widget(SelectUserWidget::class) ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton('Запустить тест', ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end() ?>