<?php
/** @var $form yii\widgets\ActiveForm */
?>
<?= $form->field($model, 'wrong_answers_params')->hiddenInput()->label(false) ?>
<h4>Таксоны для неправильных ответов</h4>
<div class="wrong-answer-list">
    <?php if (count($model->wrongAnswerTaxonNames) === 0): ?>
        <div class="row wrong-answer-list-item hide">
            <div class="col-md-5 taxon-name-select">
                <?= $form->field($model, 'wrongAnswerTaxonNames[0]')->dropDownList([], ['prompt' => '']) ?>
            </div>
            <div class="col-md-5 taxon-value-select">
                <?= $form->field($model, 'wrongAnswerTaxonValues[0]')->dropDownList([], ['prompt' => '']) ?>
            </div>
            <div class="col-md-2">
                <a href="#" class="pull-right delete-wrong-answer-row" data-index="0"><i class="glyphicon glyphicon-trash"></i></a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($model->wrongAnswerTaxonNames as $i => $taxonName): ?>
            <div class="row wrong-answer-list-item">
                <div class="col-md-5 taxon-name-select">
                    <?= $form->field($model, "wrongAnswerTaxonNames[$i]")->dropDownList([], ['prompt' => '', 'data-value' => $model->wrongAnswerTaxonNames[$i]]) ?>
                </div>
                <div class="col-md-5 taxon-value-select">
                    <?= $form->field($model, "wrongAnswerTaxonValues[$i]")->dropDownList([], ['prompt' => '', 'data-value' => $model->wrongAnswerTaxonValues[$i]]) ?>
                </div>
                <div class="col-md-2">
                    <a href="#" class="pull-right delete-wrong-answer-row" data-index="<?= $i ?>"><i class="glyphicon glyphicon-trash"></i></a>
                </div>
            </div>
        <?php endforeach ?>
    <?php endif ?>
</div>
<div>
    <a href="#" class="wrong-answer-add-item btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Добавить</a>
</div>