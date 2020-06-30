<?php
use yii\helpers\Html;
use yii\helpers\Json;
/** @var $students array */
?>
<div class="profile-tab-content payment-tab">
    <table class="table table-bordered" id="user-students-table">
        <thead>
        <tr>
            <th>Имя</th>
            <th>Возраст</th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
    <?= Html::a('Добавить ученика', ['student/create'], ['class' => 'btn btn-small', 'data-toggle' => 'modal', 'data-target' => '#create-child-modal']) ?>
    <div class="modal remote fade" id="create-child-modal">
        <div class="modal-dialog">
            <div class="modal-content"></div>
        </div>
    </div>
</div>
<?php
$studentsValue = Json::encode($students);
$deleteUrl = \yii\helpers\Url::to(['student/delete']);
$js = <<< JS
var userStudents = $studentsValue;
window.fillUserStudentsTable = function(students) {
    var table = $('#user-students-table tbody');
    table.empty();
    if (students.length) {
        students.forEach(function(student) {
            var deleteLink = $('<a/>')
                .attr({href: '#', title: 'Удалить запись'})
                .html('<i class="glyphicon glyphicon-trash"></i>')
                .on('click', function(e) {
                    e.preventDefault();
                    if (!confirm('Удалить запись?')) {
                        return false;
                    }
                    var that = this;
                    $.getJSON('$deleteUrl', {id: student.id})
                    .done(function(response) {
                        if (response && response.success) {
                            $(that).parent().parent().remove();
                            toastr.success('Запись успешно удалена');
                        }
                        else {
                            toastr.error('Во время удаления произошла ошибка');
                        }
                    })
                    .fail(function(response) {
                        toastr.error(response.responseJSON.message);
                    });
                })
            $('<tr/>')
                .append($('<td/>').text(student.name))
                .append($('<td/>').text(student.age_year))
                .append($('<td/>').append(deleteLink))
                .appendTo(table);
        });
    }
    else {
        $('<tr/>')
            .append($('<td/>').attr('colspan', 3).text('Ученики не найдены'))
            .appendTo(table);
    }
}
fillUserStudentsTable(userStudents);
$('#create-child-modal')
    .on('show.bs.modal', function() {
        $('form', this).trigger('reset');
    })
    .on('shown.bs.modal', function () {
        $('input[type=text]:eq(0)', this).focus();
    });
JS;
$this->registerJs($js);

