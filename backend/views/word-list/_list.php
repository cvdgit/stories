<?php

declare(strict_types=1);

use backend\forms\WordListForm;
use backend\widgets\grid\PjaxDeleteButton;
use backend\widgets\WordEditWidget;
use common\models\TestWord;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var WordListForm $model
 * @var DataProviderInterface $dataProvider
 */

$this->registerJs($this->renderFile('@backend/views/word-list/_words.js'));
?>
<p>
    <?= Html::a('Добавить слово', ['/word/create', 'list_id' => $model->getId()], ['class' => 'btn btn-primary', 'id' => 'create-test-word']) ?>
    <?= Html::a('Редактировать как текст', ['/word-list/text-edit', 'word_list_id' => $model->getId()], ['class' => 'btn btn-primary', 'id' => 'edit-as-text']) ?>
</p>
<h4>Слова</h4>

<div id="test-word-table">
    <?php Pjax::begin(['id' => 'pjax-words']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => static function(TestWord $model) {
                    return Html::a($model->name, ['/word/update', 'id' => $model->id], ['class' => 'update-test-word', 'data-pjax' => '0']);
                },
                'enableSorting' => false,
            ],
            [
                'attribute' => 'correct_answer',
                'enableSorting' => false,
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{copy} {delete}',
                'buttons' => [
                    'delete' => static function ($url, $model) {
                        return new PjaxDeleteButton('#', [
                            'class' => 'pjax-delete-link',
                            'delete-url' => Url::to(['/word/delete', 'id' => $model->id]),
                            'pjax-container' => 'pjax-words',
                        ]);
                    },
                    'copy' => static function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-copy"></i>', ['/word/copy', 'id' => $model->id], ['class' => 'copy-test-word', 'data-pjax' => 0, 'style' => 'margin-right: 10px']);
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?= WordEditWidget::widget([
    'modelAttribute' => 'word-list-id',
    'modelAttributeValue' => $model->getId(),
    'target' => '#edit-as-text',
]) ?>
