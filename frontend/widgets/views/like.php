<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/** @var $readOnly bool */
/** @var $model frontend\models\StoryLikeForm */

$form = ActiveForm::begin([
    'action' => ['story/like'],
    'id' => 'story-like-form',
    'options' => ['style' => 'display: inline-block'],
]);

$likeButtonOptions = ['class' => 'btn-like', 'title' => 'Понравилась', 'onclick' => new \yii\web\JsExpression('WikidsStory.storyLike(this)')];
if ($readOnly) {
    $likeButtonOptions['disabled'] = 'disabled';
    $likeButtonOptions['title'] = 'Что бы оценить историю необходимо авторизоваться';
    $likeButtonOptions['class'] .= ' disabled';
}
/** @var $like string */
if ($like !== false) {
    $likeButtonOptions['class'] .= ' user-select';
}
echo Html::button('<i class="glyphicon glyphicon-thumbs-up"></i>', $likeButtonOptions);
/** @var $likeNumber int */
echo Html::tag('span', $likeNumber, ['class' => 'like-counter']);
$dislikeButtonOptions = ['class' => 'btn-dislike', 'title' => 'Не понравилась', 'onclick' => 'WikidsStory.storyDislike(this)'];
if ($readOnly) {
    $dislikeButtonOptions['disabled'] = 'disabled';
    $dislikeButtonOptions['title'] = 'Что бы оценить историю необходимо авторизоваться';
    $dislikeButtonOptions['class'] .= ' disabled';
}
/** @var $dislike string */
if ($dislike !== false) {
    $dislikeButtonOptions['class'] .= ' user-select';
}
echo Html::button('<i class="glyphicon glyphicon-thumbs-down"></i>', $dislikeButtonOptions);
/** @var $dislikeNumber int */
echo Html::tag('span', $dislikeNumber, ['class' => 'dislike-counter']);

echo $form->field($model, 'story_id')->hiddenInput()->label(false);
echo $form->field($model, 'like')->hiddenInput()->label(false);
ActiveForm::end();

if (!$readOnly) {
    $elementId = Html::getInputId($model, 'like');
    $js = <<< JS
WikidsStory = (function() {

    var form = $("#story-like-form");

    function selectButton() {
        $('.btn-like').removeClass('user-select');
        $('.btn-dislike').removeClass('user-select');
        currentButton.addClass('user-select');
        return false;
    }
    
    var currentButton;
    
    function storyLike(obj) {
        $('#$elementId').val('1');
        form.submit();
        currentButton = $(obj);
        return false;
    }
    
    function storyDislike(obj) {
        $('#$elementId').val('0');
        form.submit();
        currentButton = $(obj);
    }
    
    function updateCounters(x, y) {
        $('.like-counter').text(x);
        $('.dislike-counter').text(y);
    }
    
    function likeOnBeforeSubmit(e) {
        e.preventDefault();
        $.ajax({
            url: form.attr("action"),
            type: form.attr("method"),
            data: new FormData(form[0]),
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
                if (data) {
                  if (data.success) {
                    selectButton();
                    updateCounters(data.like, data.dislike);
                  }
                  else {
                    $.each(data.message, function(i, message) {
                      toastr.warning('', message);
                    });
                  }
                }
                else {
                  toastr.warning('', 'Произошла неизвестная ошибка');
                }
            },
            error: function(data) {
              if (data && data.message) {
                toastr.warning('', data.message);
              }
              else {
                toastr.warning('', 'Произошла неизвестная ошибка');
              }
            }
        });
        return false;
    }
    
    form
        .on('beforeSubmit', likeOnBeforeSubmit)
        .on('submit', function(e) {
            e.preventDefault();
        });
    
    return {
        "storyLike": storyLike,
        "storyDislike": storyDislike
    };
})();
JS;
    /** @var $this yii\web\View */
    $this->registerJs($js);
}
