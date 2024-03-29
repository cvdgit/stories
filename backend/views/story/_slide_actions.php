<?php

declare(strict_types=1);

use backend\models\WordListFromStoryForm;
use common\models\Story;
use yii\bootstrap\Dropdown;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var Story $model
 * @var WordListFromStoryForm $wordListModel
 */

$this->registerJs($this->renderFile('@backend/views/story/_slides_import.js'));
$this->registerJs($this->renderFile('@backend/views/story/_replace_video.js'));
$this->registerJs($this->renderFile('@backend/views/story/_repetition.js'));
?>
<div class="dropdown pull-right">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-default">
        <i class="glyphicon glyphicon-option-vertical"></i> <b class="caret"></b>
    </a>
    <?php
    echo Dropdown::widget([
        'items' => [
            ['label' => 'Выгрузить файл', 'url' => ['/story/download', 'id' => $model->id]],
            //['label' => 'Read only история', 'url' => ['/story/readonly', 'id' => $model->id], 'linkOptions' => ['id' => 'readonly-story']],
            ['label' => 'Текст истории', 'url' => ['/story/text', 'id' => $model->id]],
            ['label' => 'Экспорт в JSON', 'url' => ['/story/json', 'id' => $model->id]],
            ['label' => 'Создать список слов (по предложениям)', 'url' => ['/word-list/make-from-story-by-proposals', 'story_id' => $model->id], 'linkOptions' => ['class' => 'story-text']],
            ['label' => 'Создать список слов (по словам)', 'url' => ['/word-list/make-from-story-by-words', 'story_id' => $model->id], 'linkOptions' => ['class' => 'story-text']],
            ['label' => 'Доступ по ссылке', 'url' => '#access-by-link-modal', 'linkOptions' => ['data-toggle' => 'modal']],
            ['label' => 'Импорт слайдов', 'url' => ['/slide-import/import', 'story_id' => $model->id], 'linkOptions' => ['id' => 'slide-import']],
            ['label' => 'Заменить видео', 'url' => ['/video/replace', 'story_id' => $model->id], 'linkOptions' => ['id' => 'video-replace']],
            ['label' => 'Создать повторения', 'url' => ['/repetition/story/create', 'story_id' => $model->id], 'linkOptions' => ['id' => 'create-repetition']],
        ],
    ]);
    ?>
</div>
<?= $this->render('_access_by_link', ['story' => $model]) ?>
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
        $('#story-text-modal').modal('show');
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
