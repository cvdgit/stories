<?php

declare(strict_types=1);

use dosamigos\selectize\SelectizeAsset;
use modules\edu\Story\AddStoryForm;
use modules\edu\Story\EduStorySearch;
use modules\edu\widgets\AdminHeaderWidget;
use modules\edu\widgets\AdminToolbarWidget;
use yii\bootstrap\ActiveForm;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var AddStoryForm $formModel
 * @var string $json
 * @var EduStorySearch $searchModel
 */

SelectizeAsset::register($this);

$this->registerJs('window.itemsData = ' . $json . ';');

$this->title = 'Истории';
$this->registerJs($this->renderFile('@modules/edu/views/admin/story/index.js'));
?>
<?= AdminToolbarWidget::widget() ?>

<?= AdminHeaderWidget::widget([
    'title' => $this->title,
    'content' => '',
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive', 'id' => 'story-list'],
    'summary' => false,
    'columns' => [
        [
            'attribute' => 'id',
            'label' => 'ИД',
        ],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'label' => 'Название истории',
            'value' => static function (array $model): string {
                return Html::a(
                    $model['title'],
                    Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/story/view', 'alias' => $model['alias']]),
                    ['target' => '_blank'],
                );
            },
        ],
        [
            'attribute' => 'author',
            'label' => 'Автор',
        ],
        [
            'attribute' => 'publishedAt',
            'format' => 'datetime',
            'label' => 'Опубликована',
        ],
        [
            'attribute' => 'path',
            'format' => 'raw',
            'label' => 'Обучение',
            'value' => static function (array $model): string {
                return $model['path'] ?? '<a href="" data-story-id="' . $model['id'] . '" class="text-danger">Выбрать</a>';
            },
        ],
        [
            'value' => static function(array $model): string {
                return Html::a('Прогресс', ['/edu/admin/story/progress', 'id' => $model['id']]);
            },
            'format' => 'raw',
        ],
    ],
]) ?>

<div class="modal rounded-0 fade" tabindex="-1" id="edu-select-modal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">Добавить в обучение</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?php $form = ActiveForm::begin(['action' => ['/edu/admin/story/add-story'], 'id' => 'edu-select-form']) ?>
            <div class="modal-body d-flex">
                <?= $form->field($formModel, 'class_id')->dropDownList([], ['prompt' => 'Выберите класс']) ?>
                <?= $form->field($formModel, 'class_program_id')->dropDownList([], ['prompt' => 'Выберите программу']) ?>
                <?= $form->field($formModel, 'topic_id')->dropDownList([], ['prompt' => 'Выберите тему']) ?>
                <?= $form->field($formModel, 'lesson_id')->dropDownList([], ['prompt' => 'Выберите урок']) ?>
                <?= $form->field($formModel, 'story_id')->hiddenInput()->label(false)->hint(null) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="submit" class="btn btn-primary" id="gpt-rewrite-text">Сохранить</button>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
