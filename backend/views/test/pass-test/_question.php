<?php

declare(strict_types=1);

use backend\assets\SvgAsset;
use backend\assets\TestQuestionAsset;
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
    display: flex;
    align-items: center;
    flex-direction: row;
    gap: 10px;
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

SvgAsset::register($this);
TestQuestionAsset::register($this);
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
        <div style="margin-left:auto;display: flex">
            <button id="content-as-html" style="margin-right: 6px" type="button" class="btn btn-primary btn-sm">HTML</button>
            <a style="margin-right: 6px" href="<?= Url::to(['/fragment-list/manage']); ?>" class="btn btn-primary btn-sm" id="manage" type="button">Управление</a>
            <button style="margin-right: 6px" class="btn btn-primary btn-sm" id="search" type="button">Поиск</button>
            <a style="margin-right: 6px" href="<?= Url::to(['/fragment-list/create', 'testing_id' => $testingId]); ?>" class="btn btn-primary btn-sm" id="create-fragment-list" type="button">Создать список</a>
            <a style="margin-right: 6px" href="<?= Url::to(['/fragment-list/select', 'testing_id' => $testingId]); ?>" class="btn btn-primary btn-sm" id="select-fragment-list" type="button">Вставить из списка</a>
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    Вставить пропуск
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" id="add">Один ответ</a></li>
                    <li><a href="#" id="add-multi">Несколько ответов</a></li>
                    <li><a href="#" id="add-region">Выбор области</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div style="min-height:300px">
        <div class="content" data-testing-id="<?= $testingId; ?>" data-question-id="<?= $model->getId(); ?>" id="content" contenteditable="true"></div>
        <textarea id="content_html" rows="10" style="width: 100%; min-height: 300px; display: none"></textarea>
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
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_functions.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_selection.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_regions.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_content.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_question.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_fragment_list.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_search.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_manage.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_html.js'));
