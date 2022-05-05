<?php
use common\helpers\Url;
use common\models\AudioFile;
use yii\bootstrap\Html;
use yii\grid\GridView;
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var backend\models\audio_file\AudioFileSearch $searchModel */
$this->title = 'Аудио файлы';
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive audio-files'],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'format' => 'raw',
                'value' => static function(AudioFile $model) {
                    return '<a data-audio="play" href="' . Url::to(['audio/play', 'id' => $model->id]) . '"><i class="glyphicon glyphicon-play"></i></a>';
                },
            ],
            'name:ntext',
            [
                'label' => 'Тест / Вопрос',
                'attribute' => 'path',
                'format' => 'raw',
                'value' => static function(AudioFile $model) {
                    $rows = [];
                    foreach ($model->storyTestQuestions as $question) {
                        $rows[] = $question->storyTest->title . ' / ' . $question->name;
                    }
                    if (count($rows) > 0) {
                        return implode('<br/>', $rows);
                    }
                }
            ],
            'created_at:datetime',
            [
                'class' => yii\grid\ActionColumn::class,
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>
</div>
<?php
$this->registerJs(<<<JS
(function() {
    $('.audio-files').on('click', '[data-audio]', function(e) {
        e.preventDefault();
        new Audio($(this).attr('href') + '&t=' + new Date().getTime()).play();
    });
})();
JS
);
