<?php

declare(strict_types=1);

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

?>
<?php $form = ActiveForm::begin([
    'id' => 'mental-maps-ai-form',
    'action' => ['/editor/mental-map/create-ai-handler']
]) ?>
<div style="display: flex; flex-direction: column; row-gap: 20px; margin-bottom: 20px">
    <div>Ментальная карта без пропусков</div>
    <div>Ментальная карта с нечетными пропусками</div>
    <div>Ментальная карта с четными пропусками</div>
</div>
<div>
    <?= Html::submitButton('Создать', ['class' => 'btn btn-primary']) ?>
    <button class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
</div>
<?php ActiveForm::end() ?>

