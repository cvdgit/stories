<?php
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\TestWordList */
$this->title = 'Изменить список слов';
$this->params['breadcrumbs'][] = ['label' => 'Test Word Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="test-word-list-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-md-6">
            <p>
                <?= Html::a('Добавить слово', ['word/create', 'list_id' => $model->id], ['class' => 'btn btn-primary', 'id' => 'create-test-word']) ?>
                <?= Html::a('Редактировать как текст', ['word-list/text-edit', 'word_list_id' => $model->id], ['class' => 'btn btn-primary', 'id' => 'edit-as-text']) ?>
            </p>
            <h4>Слова</h4>
            <table class="table table-bordered" id="test-word-table">
                <thead>
                <tr>
                    <th>Слово</th>
                    <th>Правильный ответ</th>
                    <th></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal remote fade" id="create-test-word-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="update-test-word-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?= \backend\widgets\WordEditWidget::widget([
    'modelAttribute' => 'word-list-id',
    'modelAttributeValue' => $model->id,
    'target' => '#edit-as-text',
]) ?>

<?php
$words = Json::encode($model->getTestWordsAsArray());
$deleteUrl = Url::to(['word/delete']);
$updateUrl = Url::to(['word/update']);
$copyUrl = Url::to(['word/copy']);
$js = <<< JS

$('#test-word-table').on('click', '.update-test-word,.copy-test-word', function(e) {
    e.preventDefault();
    $('#update-test-word-modal')
        .modal({'remote': $(this).attr('href')})
        .modal('show');
});

$('#update-test-word-modal').on('hide.bs.modal', function() {
    $(this).removeData('bs.modal');
    $(this).find('.modal-content').html('');
});

var words = $words;
window.fillTestWordsTable = function(params) {
    var table = $('#test-word-table tbody');
    table.empty();
    params.forEach(function(param) {
        var updateLink = $('<a/>')
            .addClass('update-test-word')
            .attr({href: '$updateUrl' + '&id=' + param.id, title: 'Изменить запись'})
            .html('<i class="glyphicon glyphicon-edit"></i>')
            .css('marginRight', '10px');
        var copyLink = $('<a/>')
            .addClass('copy-test-word')
            .attr({href: '$copyUrl' + '&id=' + param.id, title: 'Копировать запись'})
            .html('<i class="glyphicon glyphicon-copy"></i>')
            .css('marginRight', '10px');
        var deleteLink = $('<a/>')
            .attr({href: '#', title: 'Удалить запись'})
            .html('<i class="glyphicon glyphicon-trash"></i>')
            .on('click', function(e) {
                e.preventDefault();
                if (!confirm('Удалить запись?')) {
                    return false;
                }
                var that = this;
                $.getJSON('$deleteUrl', {id: param.id})
                .done(function(response) {
                    if (response && response.success) {
                        $(that).parent().parent().remove();
                    }
                })
            });
        $('<tr/>')
            .append($('<td/>').text(param.name))
            .append($('<td/>').text(param.correct_answer))
            .append($('<td/>')
                .append(updateLink)
                .append(copyLink)
                .append(deleteLink))
            .appendTo(table);
    });
}
fillTestWordsTable(words);

$('#create-test-word').on('click', function(e) {
    e.preventDefault();
    $('#create-test-word-modal').modal({'remote': $(this).attr('href')});
});

JS;
$this->registerJs($js);
