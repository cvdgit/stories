<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Пользователь: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';

$this->params['sidebarMenuItems'] = [
	['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
	['label' => 'Подписки', 'url' => ['/user/subscriptions', 'id' => $model->id]],
];
?>
<div class="row">
	<div class="col-xs-6">
		<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
		<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
		<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'status')->dropDownList(User::getStatusArray(), ['prompt' => 'Выбрать']) ?>
		<div class="form-group">
		    <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success']) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
	<div class="col-xs-6"></div>
</div>