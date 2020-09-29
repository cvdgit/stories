<?php

use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $story common\models\Story */
/** @var $source backend\models\SourcePowerPointForm */
/** @var $wordListModel backend\models\WordListFromStoryForm */

$form = ActiveForm::begin([
	'action' => ['/story/import-from-power-point'],
]);
echo $form->field($source, 'storyFile')->textInput(['readonly' => true]);
echo $form->field($source, 'slidesNumber')->textInput(['readonly' => true]);
echo $form->field($source, 'storyId')->hiddenInput()->label(false);
?>
<div class="row">
    <div class="col-lg-4 col-md-12">
        <?= Html::submitButton('Получить данные', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-lg-8 col-md-12">
        <div class="dropdown">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle btn">Действия <b class="caret"></b></a>
            <?php
            echo Dropdown::widget([
                'items' => [
                    ['label' => 'Выгрузить файл', 'url' => ['/story/download', 'id' => $story->id]],
                    ['label' => 'Read only история', 'url' => ['/story/readonly', 'id' => $story->id], 'linkOptions' => ['id' => 'readonly-story']],
                    ['label' => 'Текст истории', 'url' => ['/story/text', 'id' => $story->id]],
                    ['label' => 'Создать список слов (по предложениям)', 'url' => ['/word-list/make-from-story-by-proposals', 'story_id' => $story->id], 'linkOptions' => ['class' => 'story-text']],
                    ['label' => 'Создать список слов (по словам)', 'url' => ['/word-list/make-from-story-by-words', 'story_id' => $story->id], 'linkOptions' => ['class' => 'story-text']],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<div class="modal fade" id="story-text-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Текст истории</h4>
            </div>
            <?php $wordListForm = ActiveForm::begin([
                'action' => ['word-list/create-from-story'],
                'id' => 'word-list-from-story-form',
                'validateOnSubmit' => false,
            ]); ?>
            <div class="modal-body">
                <?= $wordListForm->field($wordListModel, 'text')->textarea(['cols' => 30, 'rows' => 20]) ?>
                <?= $wordListForm->field($wordListModel, 'story_id')->hiddenInput()->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Создать список', ['class' => 'btn btn-primary']) ?>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<< JS
$('#{$form->getId()}')
  .on('beforeSubmit', storyOnBeforeSubmit)
  .on('submit', function(e) {
    e.preventDefault();
  });

$("#readonly-story").on("click", function(e) {
    e.preventDefault();
    $.get($(this).attr("href")).done(function(data) {
        if (data && data.success) {
            toastr.success("Успешно");
        }
        else {
            toastr.error("Ошибка");
        }
    });
});

$('.story-text').on('click', function(e) {
    e.preventDefault();
    $.get($(this).attr('href')).done(function(response) {
        $('#wordlistfromstoryform-text').text(response);
        $('#story-text-modal')
            .modal('show');
    })
});

$('#word-list-from-story-form')
    .on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this),
            button = $("button[type=submit]", form);
        button.button("loading");
        var formData = form.serialize();
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: formData
        })
        .done(function(response) {
            toastr.error(response.message);
        })
        .always(function() {
            button.button("reset");
        });
    });

JS;
$this->registerJs($js);
