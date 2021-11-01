<?php
/** @var $testModel common\models\StoryTest */
/** @var $questions common\models\StoryTestQuestion[] */
$css = <<<CSS
.to-print-area table {
    font-size: 14px;
}
CSS;
$this->registerCss($css);
?>
<div class="modal-header">
    <button class="btn btn-primary print" id="print-questions">Печать</button>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
</div>
<div class="modal-body">
    <div class="to-print-area">
        <h3><?= $testModel->title ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th></th>
                    <th>Вопрос</th>
                    <th></th>
                    <th>Правильные ответы</th>
                </tr>
            </thead>
            <tbody>
            <?php $i = 1 ?>
            <?php foreach ($questions as $question): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $question->name ?></td>
                    <td><?= $i ?></td>
                    <td>
                        <?php foreach ($question->getCorrectAnswers() as $correctAnswer): ?>
                        <?= $correctAnswer->name ?>
                        <?php endforeach ?>
                    </td>
                </tr>
                <?php $i++ ?>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
</div>
<?php
$js = <<<JS
(function() {
    $('#print-questions').on('click', function() {
        $('.to-print-area').printThis();
    });
})();
JS;
$this->registerJs($js);