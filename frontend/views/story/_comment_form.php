<?php

/* @var $this yii\web\View */
/* @var $commentForm frontend\models\CommentForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

$js = <<< JS
const addCommentFocusClassName = 'add-comment-focus';
$('.comments').on('focus', '.add-comment-placeholder textarea', function() {
    let element = $(this).parent().parent();
    if (!element.hasClass(addCommentFocusClassName)) {
        element.addClass(addCommentFocusClassName);
    }
});
$('.comments').on('click', '.add-comment-close', function(e) {
    e.preventDefault();
    $(this).parent().parent().removeClass(addCommentFocusClassName).find('textarea').val('');
});
JS;
$this->registerJs($js);
?>
<?php Pjax::begin(['id' => 'comment-form-pjax', 'enablePushState' => false]); ?>
<div id="main-comment-form" class="comment-form">
    <div class="comment-form-wrapper">
        <div class="comment-logo">
            <?= Html::img($commentForm->getCurrentUserProfilePhotoPath()) ?>
        </div>
        <div class="add-comment-wrapper">
            <?php $form = ActiveForm::begin([
                'action' => ['/story/add-comment', 'id' => $commentForm->story_id],
                'enableAjaxValidation' => false,
                'options' => ['data-pjax' => true],
            ]); ?>
            <div class="add-comment-placeholder">
                <?= $form->field($commentForm, 'body', ['template' => '{input}', 'options' => ['tag' => false]])->textarea(['placeholder' => 'Оставить комментарий...', 'class' => false])->label(false) ?>
            </div>
            <div class="add-comment-controls">
                <a class="add-comment-close" href="#!">Отмена</a>
                <button type="submit" class="btn">Комментировать</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php Pjax::end(); ?>
