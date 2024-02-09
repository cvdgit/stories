<?php

declare(strict_types=1);

use backend\assets\SortableJsAsset;
use backend\Testing\Questions\Grouping\Create\CreateGroupingForm;
use backend\Testing\Questions\Grouping\Update\UpdateGroupingForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var CreateGroupingForm|UpdateGroupingForm $formModel
 * @var bool $isNewRecord
 */

SortableJsAsset::register($this);

$payload = $formModel->payload;
if ($payload === "") {
    $payload = "null";
}
$this->registerJs("window['groupingData'] = $payload;", View::POS_HEAD);
$this->registerJs($this->renderFile("@backend/views/test/grouping/_grouping.js"));
$this->registerCss($this->renderFile("@backend/views/test/grouping/_grouping.css"));
?>
<?php $form = ActiveForm::begin(['id' => 'grouping-form']); ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true]); ?>
<div>
    <label class="control-label">Группы</label>
</div>
<div class="grouping-wrap">
    <p class="no-groups">Пусто</p>
</div>
<div class="grouping-actions">
    <button id="add-group" class="btn btn-success" type="button">Добавить группу</button>
</div>
<div>
    <?= Html::hiddenInput((new ReflectionClass($formModel))->getShortName() . "[payload]", null, ["id" => "grouping_payload"]); ?>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php ActiveForm::end(); ?>
