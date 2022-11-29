<?php

declare(strict_types=1);

use backend\forms\WordListForm;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var WordListForm $model
 * @var DataProviderInterface $wordsDataProvider
 */

$this->title = 'Изменить список слов';
$this->params['breadcrumbs'][] = ['label' => 'Списки слов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;
$this->params['breadcrumbs'][] = 'Изменить';

$this->registerJs($this->renderFile('@backend/views/word-list/_update.js'));
?>
<div class="test-word-list-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]); ?>
            <div style="display: flex; flex-direction: row">
                <div style="margin-right: 10px">
                    <?= Html::a('Создать тест и историю', ['/word-list/create-story-form', 'id' => $model->getId()], ['class' => 'btn btn-default', 'data-toggle' => 'modal', 'data-target' => '#create-test-and-story-modal']) ?>
                </div>
                <div style="margin-right: 10px">
                    <?= Html::a('Создать из шаблона', ['/test-template/create-tests', 'word_list_id' => $model->getId()], ['class' => 'btn btn-default', 'data-toggle' => 'modal', 'data-target' => '#create-from-template-modal']) ?>
                </div>
                <div>
                    <?= Html::a('Запоминание стихов', ['/word-list/create-poetry', 'word_list_id' => $model->getId()], ['class' => 'btn btn-default', 'id' => 'create-poetry']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <?= $this->render('_list', [
                'model' => $model,
                'dataProvider' => $wordsDataProvider,
            ]); ?>
        </div>
    </div>
</div>

<div class="modal remote fade" id="create-test-and-story-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="create-from-template-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
