<?php

declare(strict_types=1);

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

$this->title = 'Создать доступ';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/user-access/index']) ?> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <div class="edu-user-access-form">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'user_id')->widget(SelectUserWidget::class) ?>
                <?= $form->field($model, 'status')->dropDownList(UserAccessStatus::asArray()) ?>
                <div class="form-group">
                    <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
