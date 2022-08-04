<?php
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
/* @var $this yii\web\View
 * @var $searchModel modules\edu\models\EduClassProgramSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */
$this->title = 'Программы обучения';
$this->params['breadcrumbs'][] = ['label' => 'Обучение', 'url' => ['/edu/admin/default/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCss(<<<CSS
.header-block {
    display: flex;
    padding-top: 1rem;
    padding-bottom: 0.5rem;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 20px;
}
CSS
);
?>
<div class="header-block">
    <h1 style="font-size: 32px; margin: 0 0 0.5rem 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::encode($this->title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <?= Html::a('Создать программу обучения', ['create'], ['class' => 'btn btn-default btn-sm btn-outline-secondary']) ?>
        </div>
    </div>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'table-responsive'],
    'summary' => false,
    'columns' => [
        'class.name',
        'program.name',
        ['class' => ActionColumn::class],
    ],
]) ?>
