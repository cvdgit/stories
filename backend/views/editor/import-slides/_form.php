<?php

declare(strict_types=1);

use backend\SlideEditor\ImportSlidesFromStory\ImportSlidesForm;
use backend\widgets\SelectStoryWidget;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var ImportSlidesForm $formModel
 */
?>
<?php $form = ActiveForm::begin([
    'action' => ['editor/import-slides/import'],
    'id' => 'import-slides-from-story-form',
]) ?>
<div>
    <?= $form->field($formModel, 'fromStoryId')->widget(SelectStoryWidget::class, [
        'onChange' => new \yii\web\JsExpression('() => {}')
    ]) ?>
</div>
<div>
    <?= $form->field($formModel, 'toStoryId')->hiddenInput()->label(false)->error(false)->hint(false) ?>
    <?= $form->field($formModel, 'currentSlideId')->hiddenInput()->label(false)->error(false)->hint(false) ?>
</div>
<div>
    <button type="submit" class="btn btn-primary">Импортировать слайды</button>
    <button class="btn btn-default" data-dismiss="modal">Отмена</button>
</div>
<?php ActiveForm::end() ?>
