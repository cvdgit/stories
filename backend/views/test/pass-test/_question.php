<?php

declare(strict_types=1);

use backend\models\pass_test\PassTestForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;

$this->registerCss(<<<CSS
.highlight {
    /*color: red;
    cursor:pointer;*/
    user-select: none;
}
.content:focus-visible {
    outline: none;
}
.content {
    border: 1px #d0d0d0 solid;
    padding: 10px;
    min-height: 300px;
}
.content__title label {
    margin-bottom: 0;
}
.fragment-item {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 3px 0;
}
.fragment-item:hover {
    color: #262626;
    text-decoration: none;
    background-color: #f5f5f5;
}
.fragment-input {
    display: block;
    padding: 0 10px;
}
.fragment-input input {
    cursor: pointer;
}
.fragment-title {
    display: block;
    margin-right: auto;
    padding: 0 10px;
    flex: auto;
}
.fragment-title > a {
    display: block;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333333;
    white-space: nowrap;
    text-decoration: none;
}
.fragment-title > a:focus-visible {
    outline: none;
}
.fragment-title > a:hover {
    text-decoration: none;
}
.fragment-action {
    display: block;
    padding: 0 10px;
}
.fragment-action a {
    display: block;
    width: 16px;
    height: 16px;
}
.search-fragment {
    font-weight: bold;
    cursor: pointer;
    user-select: none;
}
CSS
);
/**
 * @var View $this
 * @var PassTestForm $model
 * @var bool $isNewRecord
 * @var int $testingId
 */
?>
<?php $form = ActiveForm::begin(['id' => 'pass-test-form']) ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'view')->dropDownList($model->getViewItems(), ['prompt' => 'Выберите представление']); ?>
<?= $form->field($model, 'max_prev_items')->dropDownList($model->getMaxPrevItems())
    ->hint('При неправильном выборе возврат на указанное количество элементов'); ?>
<div>
    <div style="margin-bottom:10px;display:flex;flex-direction:row;align-items:center">
        <div class="content__title">
            <?= Html::activeLabel($model, 'content') ?>
        </div>
        <div style="margin-left:auto">
            <button class="btn btn-primary btn-sm" id="search" type="button">Поиск</button>
            <a href="<?= Url::to(['/fragment-list/create', 'testing_id' => $testingId]); ?>" class="btn btn-primary btn-sm" id="create-fragment-list" type="button">Создать список</a>
            <a href="<?= Url::to(['/fragment-list/select', 'testing_id' => $testingId]); ?>" class="btn btn-primary btn-sm" id="select-fragment-list" type="button">Вставить из списка</a>
            <button class="btn btn-primary btn-sm" id="add" type="button">Вставить пропуск</button>
        </div>
    </div>
    <div style="min-height:300px;max-height:300px;overflow-y:auto">
        <div class="content" data-question-id="<?= $model->getId(); ?>" id="content" contenteditable="true"></div>
    </div>
    <?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
</div>
<div>
    <?= Html::activeHiddenInput($model, 'payload') ?>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end() ?>
<div id="content-cache" class="hide"></div>
<?php
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_selection.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_content.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_question.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_fragment_list.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_search.js'));
