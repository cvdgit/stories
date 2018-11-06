<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Story;
use yii\helpers\Url;
use dosamigos\selectize\SelectizeTextInput;

use common\widgets\RevealWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $form yii\widgets\ActiveForm */

$url = Url::to(['/story/getfromdropbox', 'id' => $model->id]);
$script = <<< JS
function BootstrapAlert() {
    this.htmlBegin = '<div class="alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    this.htmlEnd = '</div>';
}
BootstrapAlert.prototype.success = function(message) {
    return $(this.htmlBegin + message + this.htmlEnd).addClass('alert-success');
}
BootstrapAlert.prototype.error = function(message) {
    return $(this.htmlBegin + message + this.htmlEnd).addClass('alert-danger');
}
var bAlert = new BootstrapAlert();
$('#dropbox-get-data').on('click', function() {
    $.get('$url')
        .done(function(data) {
            if (data) {
                var elem;
                if (data.error.length)
                    elem = bAlert.error(data.error);
                else
                    elem = bAlert.success(data.success);
                elem.appendTo('#alert_placeholder');
            }
		});
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
<div class="story-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php if (!empty($model->cover)): ?>
    <div class="row">
        <div class="col-xs-6 col-md-3">
            <a href="#" class="thumbnail"><img src="<?= $model->getCoverPath() ?>" alt="..."></a>
        </div>
    </div>
    <?php endif ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'user_id')->dropDownList(ArrayHelper::map(User::find()->all(), 'id', 'username'), ['prompt' => '--- select ---']) ?>
    <?php if (!$model->isNewRecord): ?>
    <div class="form-group">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <div style="height: 300px">
                    <iframe border="0" width="100%" height="100%" style="border: 0 none" src="/story/viewbyframe/<?= $model->id ?>"></iframe>
                </div>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'status')->dropDownList([Story::STATUS_DRAFT => 'Черновик', Story::STATUS_PUBLISHED => 'Публикация'], ['prompt' => '--- select ---']) ?>
	<?php endif ?>

    <?= $form->field($model, 'tagNames')->widget(SelectizeTextInput::className(), [
        'loadUrl' => ['tag/list'],
        'options' => ['class' => 'form-control'],
        'clientOptions' => [
            'plugins' => ['remove_button'],
            'valueField' => 'name',
            'labelField' => 'name',
            'searchField' => ['name'],
            'create' => true,
        ],
    ])->hint('Используйте запятые для разделения тегов') ?>

    <div class="form-group">
        <?= Html::submitButton(($model->isNewRecord ? 'Создать историю' : 'Сохранить изменения'), ['class' => 'btn btn-success']) ?>
        <?php if (!$model->isNewRecord): ?>
        <?= Html::button('Получить данные из Dropbox', ['class' => 'btn btn-primary', 'id' => 'dropbox-get-data']) ?>
        <?php endif ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
