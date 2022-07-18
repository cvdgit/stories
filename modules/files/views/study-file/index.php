<?php
use modules\files\models\StudyFileStatus;
use modules\files\models\StudyFile;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
/**
 * @var $this yii\web\View
 * @var $searchModel modules\files\forms\StudyFileSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */
$this->title = 'Файлы';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= Html::encode($this->title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <?= Html::a('Создать файл', ['create'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        'name',
        'alias',
        'folder.name',
        'created_at:datetime',
        [
            'attribute' => 'status',
            'value' => static function($model) {
                return StudyFileStatus::asText($model->status);
            }
        ],
        [
            'class' => ActionColumn::class,
            'urlCreator' => static function($action, StudyFile $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
             }
        ],
    ],
]) ?>
