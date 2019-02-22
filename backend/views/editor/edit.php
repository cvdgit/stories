<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
	<div class="col-xs-6">
	<?php $n = 1; ?>
	<?php foreach ($model->slides as $i => $slide): ?>
		<div class="row">
			<div class="col-xs-12">
				<?= $form->field($model, "slides[$i]")->textarea(['rows' => 4, 'value' => $slide])->label(Html::activeLabel($model, 'slides') . "  ". $n++) ?>
			</div>
		</div>
	<?php endforeach ?>
	</div>
</div>
<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
