<?php
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/** @var $model common\models\StoryTest */
?>
<div>
    <p>
        <?= Html::a('Создать вариант тест', ['test-variant/create', 'parent_id' => $model->id], ['class' => 'btn btn-primary', 'id' => 'create-test-variant']) ?>
    </p>
    <h4>Варианты теста</h4>
    <table class="table table-bordered" id="test-variants-table">
        <thead>
        <tr>
            <th>Вариант</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal remote fade" id="test-variant-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$testVariants = Json::encode($model->getChildrenTestsAsArray());
$deleteUrl = Url::to(['test-variant/delete']);
$js = <<< JS
var testVariants = $testVariants;
window.fillTestVariantsTable = function(params) {
    var table = $('#test-variants-table tbody');
    table.empty();
    params.forEach(function(param) {
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
            })
        $('<tr/>')
            .append($('<td/>').text(param.title))
            .append($('<td/>').text(''))
            .append($('<td/>').append(deleteLink))
            .appendTo(table);
    });
}
fillTestVariantsTable(testVariants);

$('#create-test-variant').on('click', function(e) {
    e.preventDefault();
    $('#test-variant-modal').modal({'remote': $(this).attr('href')});
});
JS;
$this->registerJs($js);