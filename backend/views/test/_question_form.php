<?php
use backend\widgets\QuestionErrorTextWidget;
use backend\widgets\QuestionSlidesWidget;
use common\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
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
                        <?= Html::a('Создать ответ', ['test/create-answer', 'question_id' => $model->getModelID()], ['class' => 'btn btn-primary']) ?>
                    </p>
                    <h4>Ответы на вопрос</h4>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['class' => 'table-responsive'],
                        'columns' => [
                            [
                                'attribute' =>'name',
                                'format' => 'raw',
                                'value' => static function($model) {
                                    return Html::a($model->name, ['test/update-answer', 'answer_id' => $model->id], ['title' => 'Перейти к редактированию']);
                                },
                            ],
                            'is_correct',
                            [
                                'class' => ActionColumn::class,
                                'template' => '{update} {delete}',
                                'urlCreator' => function($action, $model, $key, $index) {
                                    $url = '';
                                    if ($action === 'update') {
                                        $url = Url::to(['test/update-answer', 'answer_id' => $model->id]);
                                    }
                                    if ($action === 'delete') {
                                        $url = Url::to(['test/delete-answer', 'answer_id' => $model->id]);
                                    }
                                    return $url;
                                },
                            ],
                        ],
                    ]) ?>
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
})();
JS
);
