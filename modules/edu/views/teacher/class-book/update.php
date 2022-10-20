<?php

declare(strict_types=1);

use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\widgets\TeacherMenuWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var ClassBookForm $formModel
 * @var array $checkBoxList
 */

$this->title = 'Редактировать класс';
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <h1 class="h2 text-center"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/class-book/index']) ?> Редактировать класс</h1>

    <div style="margin: 20px 0 40px 0; text-align: center">
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'story-form']]) ?>
                <?= $form->field($formModel, 'name')->textInput(['autofocus' => true, 'autocomplete' => 'off']) ?>
                <?= $form->field($formModel, 'class_id')->dropDownList($formModel->getClassArray(), ['prompt' => 'Выберите класс']) ?>
                <?= $form->field($formModel, 'class_programs')->checkboxList($checkBoxList)->label(false) ?>
                <?= Html::submitButton('Сохранить', ['class' => 'btn']) ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('#classbookform-class_id').on('change', function() {

        var id = $(this).val();

        $('#classbookform-class_programs').empty();

        if (id) {

            $.getJSON('/edu/teacher/class-book/programs', {id})
                .then(function(response) {
                    if (response && response.length > 0) {
                        response.forEach(function(item) {
                            $('<div class="checkbox"><label><input type="checkbox" name="ClassBookForm[class_programs][]" value="' + item.id + '"> ' + item.name + '</label></div>')
                                .appendTo('#classbookform-class_programs')
                        });
                    }
                });
        }
    });
})();
JS
);
