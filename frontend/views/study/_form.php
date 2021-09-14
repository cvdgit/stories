<?php
use common\helpers\SmartDate;
use yii\helpers\Html;
/** @var $taskModel common\models\StudyTask */
/** @var $userProgress common\models\StudyTaskProgress */
/** @var $this yii\web\View */
$formID = 'study-task-form';
$css = <<<CSS
#study-task-action {
    text-align: center;
    max-width: 80%;
}
#study-task-action h2 {
    margin-top: 0;
}
#study-task-action p {
    text-align: justify;
}
CSS;
$this->registerCss($css);
?>
<div id="study-task-action">
    <h2><?= Html::encode($taskModel->title) ?></h2>
    <?php if (!empty($taskModel->description)): ?>
    <p><?= Html::encode($taskModel->description) ?></p>
    <?php endif ?>
    <?php if ($userProgress === null): ?>
        <?= $this->render('_begin_form', ['taskID' => $taskModel->id, 'formID' => $formID]) ?>
    <?php else: ?>
        <div>
            <span>Активирован:</span> <?= SmartDate::dateSmart($userProgress->created_at, true) ?>
        </div>
        <?= $this->render('_continue_form', ['taskID' => $taskModel->id, 'formID' => $formID]) ?>
    <?php endif ?>
</div>
<?php
$js = <<<JS
(function() {
    $('#study-task-form button[type=submit]').on('click', function() {
        $('#study-task-action').hide();
        $('<img/>', {'src': '/img/loading.gif'})
            .appendTo($('#story-container .story-no-subscription'));
    });
    $('#study-task-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: new FormData(this),
            dataType: 'html',
            cache: false,
            contentType: false,
            processData: false
        })
            .done(function(response) {
                $('#story-container').html(response);
            })
            .fail(function() {
                
            });
        return false;
    })
        .on('submit', function(e) {
            e.preventDefault();
        });
})();
JS;
$this->registerJs($js);
