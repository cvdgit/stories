<?php

declare(strict_types=1);

use modules\edu\Teacher\ClassBook\ManageTopics\ManageTopicForm;
use modules\edu\Teacher\ClassBook\ManageTopics\TopicAccessForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var array $topics
 * @var ManageTopicForm $formModel
 * @var TopicAccessForm $topicFormModel
 */
?>
<div style="text-align: initial">
    <?php $form = ActiveForm::begin([
        'action' => ['/edu/admin/class-book/save-topic-access'],
        'id' => 'topics-manage-form',
    ]); ?>
    <table class="table table-bordered table-hover" id="topic-access-list">
        <thead>
            <tr>
                <th>Предмет</th>
                <th>Тема</th>
                <th>Доступ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topics as $i => $topic): ?>
                <tr>
                    <td>
                        <?= $topic['programName']; ?>
                    </td>
                    <td>
                        <?= $topic['topicName']; ?>
                    </td>
                    <td>
                        <?= Html::checkbox('', !empty($topic['have_access']), ['value' => $topic['topicId'], 'data-program-id' => $topic['classProgramId'], 'class' => 'topic-access']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="padding: 20px 0">
        <?= Html::activeHiddenInput($formModel, 'class_book_id'); ?>
        <button type="submit" class="btn btn-small">Сохранить</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
