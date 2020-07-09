<?php
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $students */
$title = 'Обучение пользователя';
$this->setMetaTags($title,
    $title,
    '',
    $title);
?>
<h1>Обучение</h1>
<table class="table table-bordered" id="user-students-table">
    <thead>
    <tr>
        <th>Имя</th>
        <th>Дата рождения</th>
        <th></th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
<?= Html::a('Добавить ученика', ['student/create'], ['class' => 'btn btn-small', 'id' => 'create-student']) ?>
<div class="modal fade" id="create-child-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
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
                });
            var editLink = $('<a/>')
                .attr({href: '/student/update?id=' + student.id, title: 'Изменить запись'})
                .html('<i class="glyphicon glyphicon-edit"></i>')
                .on('click', function(e) {
                    e.preventDefault();
                    $('.modal-content', modal).load($(this).attr('href'), function(response) {
                        modal.modal('show');
                    });
                });
            $('<tr/>')
                .append($('<td/>').attr('width', '55%').text(student.name))
                .append($('<td/>').attr('width', '30%').text(student.birth_date))
                .append($('<td/>')
                    .attr('width', '15%')
                    .addClass('wikids-students-table-actions')
                    .append(editLink)
                    .append(deleteLink)
                )
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

var modal = $('#create-child-modal');

modal
    .on('show.bs.modal', function(e) {
        if (e.namespace === 'bs.modal') {
            $('form', this).trigger('reset');
        }
    })
    .on('shown.bs.modal', function() {
        $('input[type=text]:eq(0)', this).focus();
    });

$('#create-student').on('click', function(e) {
    e.preventDefault();
     $('.modal-content', modal).load($(this).attr('href'), function(response) {
         modal.modal('show');
     });
});

JS;
$this->registerJs($js);