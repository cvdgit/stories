<?php

declare(strict_types=1);

use backend\assets\ContextMenuAsset;
use backend\assets\FancytreeAsset;
use backend\modules\LearningPath\models\LearningPath;
use backend\modules\LearningPath\Update\UpdateNameForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/**
 * @var View $this
 * @var LearningPath $learningPath
 * @var UpdateNameForm $updateNameForm
 * @var array $trees
 */

$this->title = 'Карта знаний - ' . $learningPath->name;
ContextMenuAsset::register($this);
FancytreeAsset::register($this);
$this->registerJs($this->renderFile('@backend/modules/LearningPath/views/default/update.js'));
$this->registerCss($this->renderFile('@backend/modules/LearningPath/views/default/update.css'));
?>
<div class="row" style="margin-bottom: 20px">
    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($updateNameForm, 'name')->textInput(['maxLength' => true]); ?>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div style="display: flex; flex-direction: column; height: 100%">
    <div class="tree-container">
        <?php
        foreach ($trees as $treeKey): ?>
            <div class="tree-wrap">
                <div class="tree-actions">
                    <div class="tree-name" contenteditable="plaintext-only"></div>
                    <button class="tree-delete" type="button">&times;</button>
                </div>
                <div class="tree" data-tree="<?= $treeKey; ?>"></div>
            </div>
        <?php
        endforeach; ?>
        <div>
            <button id="add-tree" type="button">Добавить</button>
        </div>
    </div>
</div>
