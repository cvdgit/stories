<?php
use common\models\StoryTestQuestion;
use yii\helpers\Html;
?>
<div class="modal fade" id="slide-question-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Выберите вопрос</h4>
            </div>
            <div class="modal-body">
                <?= Html::dropDownList('storyQuestionList',
                    null,
                    StoryTestQuestion::questionArray(),
                    ['prompt' => 'Выбрать вопрос', 'class' => 'form-control', 'id' => 'story-question-list']) ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="StoryEditor.addQuestion()">Добавить вопрос</button>
                <button class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
