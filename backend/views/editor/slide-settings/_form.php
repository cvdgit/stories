<?php

declare(strict_types=1);

use backend\SlideEditor\SlideSettings\SlideSettingsForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var SlideSettingsForm $formModel
 * @var int $slideId
 */
?>
<?php $form = ActiveForm::begin([
    'action' => ['editor/slide-settings/save', 'id' => $slideId],
    'id' => 'slide-settings-form',
]) ?>
<div>
    <?= $form->field($formModel, 'speakSlideText')->checkbox() ?>
</div>
<div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
    <button class="btn btn-default" data-dismiss="modal">Отмена</button>
</div>
<?php ActiveForm::end() ?>
