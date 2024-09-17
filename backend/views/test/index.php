<?php

declare(strict_types=1);

use backend\Testing\TestSearch;
use common\models\test\SourceType;
use common\models\test\TestStatus;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var TestSearch $searchModel
 * @var DataProviderInterface $dataProvider
 * @var int $source
 * @var int $sourceRecordsTotal
 * @var array $columns
 * @var int $status
 */

$this->title = 'Тесты';

$this->registerCss(<<<CSS
.test-grid {
    margin-top: 20px;
}
.test-grid .summary {
    text-align: right;
}
CSS
);

$this->registerJs($this->renderFile('@backend/views/test/_index.js'));
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::encode($this->title) ?></h1>
    <?php if ($status === TestStatus::DEFAULT): ?>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <?= Html::a('Создать тест', ['create', 'source' => $source], ['class' => 'btn btn-primary']); ?>
        </div>
    </div>
    <?php endif ?>
</div>

<div>
    <?php $form = ActiveForm::begin([
        'id' => 'testing-filter-form',
        'options' => ['class' => 'form-inline'],
    ]); ?>
    <?= $form->field($searchModel, 'with_repetition')->checkbox(); ?>
    <?php ActiveForm::end(); ?>
</div>

<?= Nav::widget([
    'options' => ['class' => 'nav nav-tabs material-tabs'],
    'items' => array_merge(SourceType::asNavItems($source), TestStatus::templatesNavItem()),
]); ?>

<?php if (Yii::$app->user->can('admin') && $searchModel->isNeoTest()): ?>
    <?= Html::tag(
        'div',
        Html::a('Очистить историю по всем тестам (' . ($sourceRecordsTotal === 0 ? 'нет записей' : $sourceRecordsTotal) . ')', ['history/clear-all-by-source', 'source' => $source], ['class' => 'btn btn-danger pull-right']),
        ['class' => 'clearfix', 'style' => 'padding: 20px 0 0 0']); ?>
<?php endif; ?>

<div class="tests-wrap">
    <?php Pjax::begin(['id' => 'pjax-tests']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => ['class' => 'table-responsive test-grid'],
        'layout' => "{items}\n{summary}\n{pager}",
        'columns' => $columns,
    ]); ?>
    <?php Pjax::end(); ?>
</div>
