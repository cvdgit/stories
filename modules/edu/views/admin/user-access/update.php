<?php

use backend\widgets\SelectUserWidget;
use modules\edu\models\EduUserAccess;
use modules\edu\models\UserAccessStatus;
use modules\edu\widgets\AdminToolbarWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model EduUserAccess
 */

$this->title = 'Доступ: ' . $model->user->getProfileName();
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/user-access/index']) ?> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="edu-lesson-form">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'user_id')->widget(SelectUserWidget::class, [
                    'userModel' => $model->user,
                ]) ?>
                <?= $form->field($model, 'status')->dropDownList(UserAccessStatus::asArray()) ?>
                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
