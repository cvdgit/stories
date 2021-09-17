<?php
/** @var $this yii\web\View */
/** @var $model backend\models\UserCreateForm */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = 'Создание пользователя';
$this->params['breadcrumbs'] = [
    ['label' => 'Все пользователи', 'url' => ['user/index']],
    $this->title,
];
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-6">
        <div class="user-create">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'email')->textInput(['maxLength' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput(['maxLength' => true]) ?>
            <?= $form->field($model, 'role')->dropDownList($model->rolesList()) ?>
            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>