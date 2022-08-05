<?php

declare(strict_types=1);

use modules\edu\forms\teacher\StudentForm;
use modules\edu\widgets\TeacherMenuWidget;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var StudentForm $formModel
 */

$this->title = 'Создание ученика';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1 class="text-center">Новый ученик</h1>
    <div style="margin: 20px 0 40px 0; text-align: center">
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
                <?php $form = ActiveForm::begin([
                    'options' => [
                        'class' => 'story-form',
                    ],
                ]) ?>
                <?= $form->field($formModel, 'name')->textInput(['autofocus' => true, 'autocomplete' => 'off']) ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
