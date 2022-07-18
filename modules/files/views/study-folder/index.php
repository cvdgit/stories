<?php
use modules\files\models\StudyFolder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
/**
 * @var $this yii\web\View
 * @var $searchModel modules\files\forms\StudyFolderSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 **/
$this->title = 'Папки';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= Html::encode($this->title) ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group">
            <?= Html::a('Создать папку', ['create'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => ['class' => 'table-responsive'],
    'columns' => [
        'name',
        'title:ntext',
        'created_at:datetime',
        [
            'attribute' => 'visible',
            'value' => static function($model) {
                return (int) $model->visible === 1 ? 'Да' : 'Нет';
            }
        ],
        [
            'attribute' => 'folderFilesCount',
            'label' => 'Количество файлов',
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{update} {delete}',
            'urlCreator' => static function($action, StudyFolder $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
             }
        ],
    ],
]) ?>
