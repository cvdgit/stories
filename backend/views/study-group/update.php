<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\StudyGroup */
/* @var $usersDataProvider yii\data\ActiveDataProvider */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="study-group-update">
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-8">
            <div class="study-group-form">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $this->render('_users', ['groupModel' => $model, 'usersDataProvider' => $usersDataProvider]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?= $this->render('_import_users_from_text', ['groupModel' => $model]) ?>
        </div>
    </div>
</div>