<?php

declare(strict_types=1);

use modules\testing\forms\PoetryForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var PoetryForm $model
 * @var bool $isNewRecord
 */

$this->registerCss(<<<CSS

CSS
);
?>
<?php $form = ActiveForm::begin(['id' => 'poetry-form']) ?>
<?= $form->field($model, 'name') ?>
<div>

</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end() ?>
