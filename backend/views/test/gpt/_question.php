<?php

declare(strict_types=1);

use backend\assets\MainAsset;
use backend\Testing\Questions\Gpt\Create\GptQuestionCreateForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\web\View;

/**
 * @var View $this
 * @var GptQuestionCreateForm $formModel
 * @var bool $isNewRecord
 * @var array $prompts
 */

MainAsset::register($this);

$this->registerJs($this->renderFile("@backend/views/test/gpt/_question.js"));
$this->registerCss($this->renderFile("@backend/views/test/gpt/_question.css"));
?>
<?php
$form = ActiveForm::begin(['id' => 'gpt-question-form']) ?>
<?= $form->field($formModel, 'name')->textInput(['maxlength' => true]) ?>
<?= $form->field($formModel, 'job')->textarea(['rows' => 10]) ?>
<div>
    <?= $form->field($formModel, 'promptId')->dropDownList($prompts, ['prompt' => 'Выберите промт']) ?>
    <div style="margin-bottom: 30px">
        <button id="prompt-update" style="display: <?= empty($formModel->promptId) ? 'none' : 'inline-block' ?>"
                type="button" class="btn btn-success btn-sm">Изменить
        </button>
        <button id="run-job" style="display: <?= empty($formModel->promptId) ? 'none' : 'inline-block' ?>"
                type="button" class="btn btn-danger btn-sm">Запустить
        </button>
        <button id="prompt-create" type="button" class="btn btn-primary btn-sm">Создать</button>
    </div>
</div>
<div>
    <?= Html::submitButton($isNewRecord ? 'Создать вопрос' : 'Сохранить изменения', ['class' => 'btn btn-primary']); ?>
</div>
<?php
ActiveForm::end(); ?>

<div class="modal rounded-0 fade" tabindex="-1" id="prompt-create-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">Создать промт</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body d-flex">
                <div style="margin-bottom: 10px">
                    <input class="form-control" id="gpt-create-prompt-name" type="text">
                </div>
                <div style="min-height: 400px">
                    <div contenteditable="plaintext-only" style="margin-bottom: 20px; border: 1px #eee solid"
                         class="form-control textarea" id="gpt-create-prompt"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success" id="gpt-prompt-create">Создать</button>
            </div>
        </div>
    </div>
</div>

<div class="modal rounded-0 fade" tabindex="-1" id="prompt-update-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">Изменить промт</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body d-flex">
                <div style="margin-bottom: 10px">
                    <input class="form-control" id="gpt-prompt-name" type="text">
                </div>
                <div style="min-height: 400px">
                    <div id="gpt-rewrite-text-prompt-wrap">
                        <div contenteditable="plaintext-only" style="margin-bottom: 20px; border: 1px #eee solid"
                             class="form-control textarea" id="gpt-prompt"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-success" id="gpt-prompt-save">Сохранить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal rounded-0 fade" tabindex="-1" id="run-job-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="height: 90%">
        <div class="modal-content" style="height: 100%; display: flex; flex-direction: column;">
            <div class="modal-header" style="display: flex; justify-content: space-between">
                <h5 class="modal-title" style="margin-right: auto">Запустить</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="display: flex; justify-content: space-between; flex-direction: column; height: 90%">
                <div style="display: flex; flex-direction: column">
                    <h5>Задание:</h5>
                    <pre id="run-job-text" class="textarea"></pre>
                </div>
                <div id="gpt-message-list" style="display: flex; flex-direction: column; flex: 1; overflow-y: auto; margin-bottom: 20px">
                </div>
                <div style="display: flex; position: relative; flex-direction: row; justify-content: space-between; height: 100px; align-items: center">
                    <div style="display: flex; flex-direction: column; flex: 1; margin-right: 10px">
                        <h5>Ответ пользователя:</h5>
                        <textarea id="gpt-user-response" class="textarea" style="flex: 1; min-height: 50px; height: 50px;"></textarea>
                    </div>
                    <button id="job-send" class="btn" type="button">Проверить</button>
                    <div id="job-send-loader" style="display: none; cursor: wait; position: absolute; left: 0; top: 0; right: 0; bottom: 0; align-items: center; justify-content: center; background-color: #eee; border-radius: 8px">
                        <img src="/img/loading.gif" width="30" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
