<?php

use common\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $model common\models\StoryTest */
/** @var $form yii\widgets\ActiveForm */
/** @var $dataProvider yii\data\ActiveDataProvider */
?>

<div class="story-test-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?php if (!$model->isNewRecord): ?>
    <div>
        <p>
            <?= Html::a('Новый вопрос', ['test/create-question', 'test_id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive'],
            'columns' => [
                'id',
                'name',
                [
                    'class' => ActionColumn::class,
                    'template' => '{update} {delete}',
                    'urlCreator' => function($action, $model, $key, $index) {
                        $url = '';
                        if ($action === 'update') {
                            $url = Url::to(['test/update-question', 'question_id' => $model->id]);
                        }
                        if ($action === 'delete') {
                            $url = Url::to(['test/delete-question', 'question_id' => $model->id]);
                        }
                        return $url;
                    },
                ],
            ],
        ]) ?>
    </div>
    <?php endif ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
