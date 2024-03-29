<?php

declare(strict_types=1);

use backend\assets\MainAsset;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $model
 * @var DataProviderInterface $dataProvider
 * @var array $routes
 */

MainAsset::register($this);
$this->registerJs($this->renderFile("@backend/views/test/_gpt_import.js"));
?>
<div>
    <div class="clearfix">
        <div style="display: flex; justify-content: space-between">
            <div>
                <?php if ($model->isAnswerTypeInput()): ?>
                    <a href="<?= Url::to(['question-input/create', 'test_id' => $model->id]) ?>" data-toggle="modal" data-target="#create-test-question-modal" type="button" class="btn btn-primary">Создать вопрос</a>
                <?php else: ?>
                <div class="dropdown" style="display: inline-block">
                    <button type="button" data-toggle="dropdown" class="btn btn-primary">Создать вопрос <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <?php foreach ($routes as $label => $route): ?>
                        <li><?= Html::a($label, $route) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif ?>
            </div>
            <div>
                <a data-quiz-id="<?= $model->id; ?>" id="gpt-import" class="btn" href="" style="padding: 0">
                    <img style="width:30px" src="/img/chatgpt-icon.png" alt="">
                </a>
                <?= Html::a('Импортировать вопросы из списка слов', ['test/import/from-word-list', 'test_id' => $model->id], ['class' => 'btn btn-default', 'style' => 'margin-left: 20px', 'id' => 'import-from-word-list']) ?>
            </div>
        </div>
    </div>
    <h4>Вопросы теста</h4>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive', 'id' => 'questions-grid'],
        'rowOptions' => static function(StoryTestQuestion $model, $key, $index, $grid) {
            $options = [];
            if (!$model->isCorrectData()) {
                $options['class'] = 'danger';
            }
            return $options;
        },
        'columns' => [
            [
                'format' => 'raw',
                'value' => static function(StoryTestQuestion $model) {
                    $type = $model->getQuestionType();
                    $class = 'glyphicon ';
                    if ($type->isSingle()) {
                        $class .= 'glyphicon-record';
                    }
                    if ($type->isMultiple()) {
                        $class .= 'glyphicon-check';
                    }
                    if ($type->isRegion()) {
                        $class .= 'glyphicon-picture';
                    }
                    if ($type->isSequence()) {
                        $class .= 'glyphicon-tasks';
                    }
                    $html = Html::tag('i', '', ['class' => $class, 'title' => $type->getTypeName()]);
                    if ($model->mix_answers) {
                        $html .= ' ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-random', 'title' => 'Перемешивать ответы']);
                    }
                    return $html;
                },
            ],
            [
                'attribute' =>'name',
                'format' => 'raw',
                'value' => static function(StoryTestQuestion $model) {
                    return Html::a($model->name, $model->getUpdateRoute(), ['title' => 'Перейти к редактированию']);
                },
            ],
            [
                'label' => 'Ответов',
                'value' => static function(StoryTestQuestion $model) {
                    $value = count($model->storyTestAnswers);
                    if (($correctCount = count($model->getCorrectAnswers())) > 0) {
                        $value .= ' (' . $correctCount . ')';
                    }
                    return $value;
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{delete}',
                'urlCreator' => function($action, $model, $key, $index) {
                    $url = '';
                    if ($action === 'delete') {
                        $route = ['test/delete-question', 'question_id' => $model->id];
                        if ($model->typeIsRegion()) {
                            $route = ['question/delete', 'id' => $model->id];
                        }
                        if ($model->typeIsSequence()) {
                            $route = ['test/question-sequence/delete', 'id' => $model->id];
                        }
                        $url = Url::to($route);
                    }
                    return $url;
                },
            ],
        ],
    ]) ?>
</div>
<div class="modal remote fade" id="import-from-word-list-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal fade" id="create-test-question-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<?php
$js = <<< JS
$('#import-from-word-list').on('click', function(e) {
    e.preventDefault();
    $('#import-from-word-list-modal').modal({'remote': $(this).attr('href')});
});
$('#import-from-word-list-modal').on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
});

Sortable.create($('#questions-grid tbody')[0], {
    ghostClass: 'wikids-sortable-ghost',
    handle: 'tr',
    onUpdate: function() {
        var ids = [];
        $('#questions-grid tbody tr[data-key]').each(function(i, elem) {
            ids.push($(elem).data('key'));
        });
        $('#storytest-sortable').val(ids.join(','));
    },
    onStart: function () {
		$('nav#w2').hide();
	},
	onEnd: function () {
        $('nav#w2').show();
	}
});

$('#create-test-question-modal')
    .on('hide.bs.modal', function() {
        $(this).removeData('bs.modal');
        $(this).find('.modal-content').html('');
    });
JS;
$this->registerJs($js);
