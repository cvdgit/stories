<?php
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
    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'story_test_id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
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
            <div class="form-group form-group-controls">
                <?= Html::submitButton(($isNewRecord ? 'Создать' : 'Изменить') . ' вопрос', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-6">
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