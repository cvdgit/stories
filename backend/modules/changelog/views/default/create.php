<?php

declare(strict_types=1);

use backend\modules\changelog\ChangelogCreate\CreateChangelogForm;
use backend\modules\changelog\models\ChangelogStatus;
use dosamigos\selectize\SelectizeTextInput;
use vova07\imperavi\Widget;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var View $this
 * @var CreateChangelogForm $formModel
 */

$this->title = 'Новая запись';
?>
<h1 class="h2 page-header">
    <a href="<?= Url::to(['/changelog/default/index']); ?>"><i class="glyphicon glyphicon-arrow-left back-arrow"></i></a>
    <?= Html::encode($this->title) ?>
</h1>

<div class="row">
    <div class="col-lg-8">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($formModel, 'title')->textInput(['maxlength' => 250]) ?>
        <?= $form->field($formModel, 'text')->widget(Widget::class, [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'imageUpload' => Url::to(['/changelog/default/image-upload']),
                'plugins' => [
                    'fullscreen',
                    'imagemanager',
                ],
            ],
        ]); ?>
        <?= $form->field($formModel, 'created')->textInput(['type' => 'date']); ?>
        <?= $form->field($formModel, 'tags')->widget(SelectizeTextInput::class, [
            'loadUrl' => ['/changelog/default/tags'],
            'options' => ['class' => 'form-control'],
            'clientOptions' => [
                'plugins' => ['remove_button'],
                'valueField' => 'name',
                'labelField' => 'name',
                'searchField' => ['name'],
                'create' => true,
            ],
        ]); ?>
        <?= $form->field($formModel, 'status')->dropDownList(ChangelogStatus::allItems(), ['prompt' => 'Выберите статус']); ?>
        <div class="form-group">
            <?= Html::submitButton('Создать', ['class' => 'btn btn-success']); ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
