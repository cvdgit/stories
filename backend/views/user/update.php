<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use common\helpers\UserHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Пользователь: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Изменить';

$this->params['sidebarMenuItems'] = [
	['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
	['label' => 'Подписка', 'url' => ['/user/subscriptions', 'id' => $model->id]],
];
?>
<div class="row">
	<div class="col-xs-6">
		<h2 class="page-header"><?= Html::encode($this->title) ?></h2>
		<?php $form = ActiveForm::begin(); ?>
		<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
		<?= $form->field($model, 'status')->dropDownList(UserHelper::getStatusArray(), ['prompt' => 'Выбрать']) ?>
		<div class="form-group">
		    <?= Html::submitButton('Сохранить изменения', ['class' => 'btn btn-success']) ?>
		</div>
		<?php ActiveForm::end(); ?>
	</div>
</div>
