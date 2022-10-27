<?php

declare(strict_types=1);

use backend\widgets\grid\PjaxDeleteButton;
use backend\widgets\grid\UpdateButton;
use backend\widgets\QuestionErrorTextWidget;
use backend\widgets\QuestionSlidesWidget;
use common\models\StoryTestAnswer;
use vova07\imperavi\Widget;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/** @var $this yii\web\View */
/** @var $model backend\models\question\QuestionModel */
/** @var $form yii\widgets\ActiveForm */
/** @var $dataProvider yii\data\ActiveDataProvider */
$isNewRecord = $model instanceof \backend\models\question\CreateQuestion;
?>
<div class="story-test-form">
    <?php if (!$isNewRecord): ?>
    <?= QuestionErrorTextWidget::widget(['questionModel' => $model->getModel()]) ?>
    <?php endif ?>
    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'hint')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'type')->dropDownList(\backend\models\question\QuestionType::asArray()) ?>
            <?= $form->field($model, 'mix_answers')->checkbox() ?>
            <?= $form->field($model, 'imageFile')->fileInput() ?>
            <?php if (!$isNewRecord && $model->haveImage()): ?>
                <div style="padding: 20px 0; text-align: center">
                    <?= Html::img($model->getImageUrl(), ['style' => 'max-width: 330px']) ?>
                    <div>
                        <?= Html::a('Удалить изображение', ['question/delete-image', 'id' => $model->getModelID()]) ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if (!$isNewRecord): ?>
                <?= QuestionSlidesWidget::widget(['model' => $model->getModel()]) ?>
            <?php endif ?>

            <?php if (!$isNewRecord): ?>
                <div style="padding-bottom:20px">
                    <?= $form->field($model, 'audio_file_id')
                        ->widget(\backend\widgets\SelectAudioFileWidget::class, [
                            'audioFile' => $model->getAudioFile(),
                        ])
                        ->hint(Html::button('Создать аудио файл', [
                                'class' => 'btn btn-xs btn-primary',
                                'data-toggle' => 'modal',
                                'data-target' => '#create-audio-file-modal',
                            ]) . ' ' . Html::button('Прослушать', ['class' => 'btn btn-xs btn-default', 'id' => 'play-audio']))
                    ?>
                </div>
            <?php endif ?>

            <?= $form->field($model, 'incorrect_description')->widget(Widget::class, [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 150,
                    'buttons' => ['html', 'bold', 'italic', 'deleted', 'unorderedlist', 'orderedlist', 'alignment', 'horizontalrule'],
                    'plugins' => [
                        'fontcolor',
                        'fontsize',
                    ],
                ],
            ]); ?>

            <?= $form->field($model, 'story_test_id')->hiddenInput()->label(false) ?>

            <div class="form-group form-group-controls">
                <?= Html::submitButton(($isNewRecord ? 'Создать' : 'Сохранить'), ['class' => 'btn btn-success', 'name' => 'action', 'value' => 'save']) ?>
                <?php if (!$isNewRecord): ?>
                    <?= Html::submitButton('Сохранить и вернуться к тесту', ['class' => 'btn btn-link', 'name' => 'action', 'value' => 'save-and-return']) ?>
                <?php endif ?>
            </div>
            <?php ActiveForm::end(); ?>

            <?php if (!$isNewRecord): ?>
                <?= $this->render('_audio_file_modal', ['updateQuestionModel' => $model]) ?>
            <?php endif ?>
        </div>
        <div class="col-lg-6">
            <?php if (!$isNewRecord): ?>
                <div>
                    <p>
                        <?= Html::a('Создать ответ', ['/answer/create', 'question_id' => $model->getModelID()], ['class' => 'btn btn-primary', 'id' => 'create-answer']) ?>
                    </p>
                    <h4>Ответы на вопрос</h4>
                    <div class="answers-wrap">
                        <?php Pjax::begin(['id' => 'pjax-answers']); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'options' => ['class' => 'table-responsive'],
                            'summary' => false,
                            'columns' => [
                                [
                                    'attribute' =>'name',
                                    'format' => 'raw',
                                    'value' => static function($model) {
                                        return Html::a($model->name, ['/answer/update', 'id' => $model->id], ['data-pjax' => 0, 'class' => 'answer-update']);
                                    },
                                    'enableSorting' => false,
                                ],
                                [
                                    'attribute' => 'image',
                                    'format' => 'raw',
                                    'value' => static function(StoryTestAnswer $model) {
                                        if ($model->haveImage()) {
                                            return Html::img($model->getImageUrl());
                                        }
                                        return '';
                                    },
                                    'enableSorting' => false,
                                ],
                                [
                                    'attribute' => 'is_correct',
                                    'value' => static function(StoryTestAnswer $model) {
                                        return $model->answerIsCorrect() ? 'Да' : 'Нет';
                                    },
                                    'enableSorting' => false,
                                ],
                                [
                                    'class' => ActionColumn::class,
                                    'template' => '{update} {delete}',
                                    'controller' => 'answer',
                                    'buttons' => [
                                        'update' => static function($url, $model, $key) {
                                            return (new UpdateButton($url, ['class' => 'answer-update']))();
                                        },
                                        'delete' => static function($url) {
                                            return new PjaxDeleteButton('#', [
                                                'class' => 'pjax-delete-link',
                                                'delete-url' => $url,
                                                'pjax-container' => 'pjax-answers',
                                            ]);
                                        }
                                    ],
                                ],
                            ],
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('#play-audio').on('click', function() {
        var selectedId = $('#updatequestion-audio_file_id').find('option:selected').val();
        if (selectedId) {
            new Audio('/admin/index.php?r=audio/play&id=' + selectedId + '&t=' + new Date().getTime()).play();
        }
    });

    const createAnswerDialog = new RemoteModal({id: 'create-answer-modal', title: 'Новый ответ'});
    $('#create-answer').on('click', function(e) {
        e.preventDefault();
        createAnswerDialog.show({url: $(this).attr('href'), callback: function() {
            const formElement = document.getElementById('answer-form');
            attachBeforeSubmit(formElement, (form) => {
                sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
                    .done((response) => {
                        if (response && response.success) {
                            createAnswerDialog.hide();
                            toastr.success(response.message);
                            $.pjax.reload('#pjax-answers', {timeout: 3000});
                        }
                    })
            });
        }});
    });

    const updateAnswerDialog = new RemoteModal({id: 'update-answer-modal', title: 'Редактировать ответ'});
    $('.answers-wrap').on('click', '.answer-update', function(e) {
        e.preventDefault();
        updateAnswerDialog.show({url: $(this).attr('href'), callback: function() {
            const formElement = document.getElementById('answer-form');
            attachBeforeSubmit(formElement, (form) => {
                sendForm($(form).attr('action'), $(form).attr('method'), new FormData(form))
                    .done((response) => {
                        if (response && response.success) {
                            updateAnswerDialog.hide();
                            toastr.success(response.message);
                            $.pjax.reload('#pjax-answers', {timeout: 3000});
                        }
                    })
            });
        }});
    });
})();
JS
);
