<?php
use modules\edu\models\EduLesson;
use modules\edu\widgets\AdminHeaderWidget;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
/**
 * @var View $this
 * @var DataProviderInterface $storiesDataProvider
 * @var EduLesson $model
 */
$this->registerCss(<<<CSS
.header-block {
    display: flex;
    padding-top: 1rem;
    padding-bottom: 0.5rem;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}
CSS
);
?>
<div>
    <div class="header-block">
        <h3 style="margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h4">Истории</h3>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить историю', ['select-story', 'lesson_id' => $model->id], ['data-toggle' => 'modal', 'data-target' => '#select-story-modal', 'class' => 'btn btn-default btn-sm btn-outline-secondary']) ?>
            </div>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'pjax-stories']) ?>
        <?= GridView::widget([
            'dataProvider' => $storiesDataProvider,
            'summary' => false,
            'columns' => [
                'title',
                ['class' => ActionColumn::class],
            ],
        ]) ?>
    <?php Pjax::end() ?>
</div>

<div class="modal remote fade" tabindex="-1" id="select-story-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
