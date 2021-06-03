<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$form = ActiveForm::begin([
    'id' => 'block-form',
]);
/** @var $model backend\models\editor\BaseForm */
/** @var $this yii\web\View  */
?>
<?= $this->render($model->view, ['form' => $form, 'model' => $model]) ?>

<?php
echo $form->field($model, 'slide_id')->hiddenInput()->label(false);
echo $form->field($model, 'block_id', ['inputOptions' => ['class' => 'editor-block-id']])->hiddenInput()->label(false);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'style' => 'margin-right: 20px']);
ActiveForm::end();
