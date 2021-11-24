<?php
/** @var $rows array */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4 class="modal-title">Детализация теста</h4>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Вопрос</th>
            <th>Ответ</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr <?= (int)$row['correct'] === 1 ? '' : 'class="danger"' ?>>
                <td><?= $row['question_name'] ?></td>
                <td><?= $row['user_answers'] ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button class="btn btn-small btn-default" data-dismiss="modal">Отмена</button>
</div>