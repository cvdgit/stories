<?php

declare(strict_types=1);

use backend\assets\SvgAsset;
use backend\assets\TestQuestionAsset;
use backend\models\pass_test\PassTestForm;
use backend\widgets\QuestionSlidesWidget;
use common\assets\panzoom\PanzoomAsset;
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
    min-height: auto;
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
PanzoomAsset::register($this);

$this->registerCss(<<<CSS
.dropdown-submenu {
	position: relative;
}
.dropdown-submenu > .dropdown-menu {
	top: 0;
	left: 100%;
	margin-top: -6px;
	margin-left: -1px;
	-webkit-border-radius: 0 6px 6px 6px;
	-moz-border-radius: 0 6px 6px 6px;
	border-radius: 0 6px 6px 6px;
}
.dropdown-submenu:hover > .dropdown-menu {
	display: block;
}
.dropdown-submenu > a:after {
	display: block;
	content: " ";
	float: right;
	width: 0;
	height: 0;
	border-color: transparent;
	border-style: solid;
	border-width: 5px 0 5px 5px;
	border-left-color: #cccccc;
	margin-top: 5px;
	margin-right: -10px;
}
.dropdown-submenu:hover > a:after {
	border-left-color: #ffffff;
}
.dropdown-submenu.pull-left {
	float: none;
}
.dropdown-submenu.pull-left > .dropdown-menu {
	left: -100%;
	margin-left: 10px;
	-webkit-border-radius: 6px 0 6px 6px;
	-moz-border-radius: 6px 0 6px 6px;
	border-radius: 6px 0 6px 6px;
}

#content .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    display: none;
    float: left;
    min-width: 160px;
    padding: 5px 0;
    margin: 2px 0 0;
    font-size: 14px;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.15);
    border-radius: 4px;
    -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
}
#content .open > .dropdown-menu {
    display: block;
}
#content .dropdown-menu {
  position: fixed !important;
}
.image-container-wrapper {
    overflow: hidden;
    margin-top: 10px;
    border: 1px #d0d0d0 solid;
}
.image-container-wrapper svg {
    outline: 0;
}
.redactor-editor {
    overflow: hidden !important;
}
#to-gpt-fragments .label {
    font-size: 90%;
}
CSS
);
$this->registerJs(<<<JS
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
JS
);
?>
<?php $form = ActiveForm::begin(['id' => 'pass-test-form']) ?>
<?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>
<?= $form->field($model, 'view')->dropDownList($model->getViewItems(), ['prompt' => 'Выберите вид']); ?>
<?= $form->field($model, 'max_prev_items')->dropDownList($model->getMaxPrevItems())
    ->hint('При неправильном выборе возврат на указанное количество элементов'); ?>
<div class="content-wrap">
    <div style="margin-bottom:10px;display:flex;flex-direction:row;align-items:center">
        <div class="content__title">
            <?= Html::activeLabel($model, 'content') ?>
        </div>
        <div style="margin-left:auto;display: flex">
            <a style="margin-right: 6px" href="<?= Url::to(['/fragment-list/manage']); ?>" class="btn btn-primary btn-sm" id="manage" type="button">Управление</a>
            <button style="margin-right: 6px" class="btn btn-primary btn-sm" id="search" type="button">Поиск</button>

            <div class="dropdown" data-toggle="tooltip" title="Добавить фрагменты">
                <button data-toggle="dropdown" style="margin-right: 6px" class="btn btn-success btn-sm dropdown-toggle" type="button">
                    Заполнить
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li style="height: 36px"><a href="#" id="random">Случайным образом</a></li>
                    <li style="height: 36px">
                        <a href="#" id="gpt-generate-gaps">
                            <img style="width:30px" src="/img/chatgpt-icon.png" alt="">
                            Добавить пропуски
                        </a>
                    </li>
                    <li style="height: 36px">
                        <a href="#" id="gpt-add-incorrect">
                            <img style="width:30px" src="/img/chatgpt-icon.png" alt="">
                            Добавить неправильные ответы к пропускам
                        </a>
                    </li>
                </ul>
            </div>

            <a style="margin-right: 6px" href="<?= Url::to(['/fragment-list/create', 'testing_id' => $testingId]); ?>" class="btn btn-primary btn-sm" id="create-fragment-list" type="button">Создать список</a>
            <a style="margin-right: 6px" href="<?= Url::to(['/fragment-list/select', 'testing_id' => $testingId]); ?>" class="btn btn-primary btn-sm" id="select-fragment-list" type="button">Вставить из списка</a>
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    Вставить пропуск
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" data-fragment-type="single" class="add-fragment">Один ответ</a></li>
                    <li><a href="#" data-fragment-type="multi" class="add-fragment">Несколько ответов</a></li>
                    <li><a href="#" data-fragment-type="region" class="add-fragment">Выбор области</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div style="min-height:300px;position:relative">
        <div class="content" data-testing-id="<?= $testingId; ?>" data-question-id="<?= $model->getId(); ?>" id="content" contenteditable="true"></div>

        <?= \vova07\imperavi\Widget::widget([
            'selector' => '#content',
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 200,
                'buttons' => ['html', 'bold', 'italic', 'deleted', 'alignment', 'image'],
                'imageUpload' => Url::to(['/test/pass-test/image-upload']),
                'plugins' => [
                    'table',
                    'imagemanager',
                    //'fullscreen',
                ],
                'replaceDivs' => false,
                'paragraphize' => false,
                'linebreaks' => true,
            ],
        ]); ?>

        <div id="add-fragment" style="position: absolute; display: none; z-index: 999">
            <div class="dropdown">
                <button title="Вставить пропуск" class="btn btn-success btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-plus"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="#" data-fragment-type="single" class="add-fragment">Один ответ</a></li>
                    <li><a href="#" data-fragment-type="multi" class="add-fragment">Несколько ответов</a></li>
                    <li><a href="#" data-fragment-type="region" class="add-fragment">Выбор области</a></li>
                    <li role="separator" class="divider"></li>
                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Пропуски</a>
                        <ul class="dropdown-menu" id="last-fragments">
                            <li class="disabled"><a href="#">Пусто</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
    <?php if (!$isNewRecord): ?>
        <?= QuestionSlidesWidget::widget(['modelId' => $model->getId()]); ?>
    <?php endif ?>
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
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_random.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_gpt_random.js'));
$this->registerJs($this->renderFile('@backend/views/test/pass-test/_gpt_gaps.js'));
