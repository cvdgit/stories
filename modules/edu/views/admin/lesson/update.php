<?php
use modules\edu\models\EduLesson;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
/**
 * @var $this View
 * @var $model EduLesson
 * @var $storiesDataProvider DataProviderInterface
 */
$this->title = 'Урок: ' . $model->name;
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/topic/update', 'id' => $model->topic_id]) ?> <?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <div class="edu-lesson-form">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'topic_id')->dropDownList($model->getTopicArray(), ['disabled' => true]) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="col-lg-6">
            <?= $this->render('_stories', ['storiesDataProvider' => $storiesDataProvider, 'model' => $model]) ?>
        </div>
    </div>
</div>
