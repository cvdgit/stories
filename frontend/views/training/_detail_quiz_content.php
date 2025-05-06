<?php

declare(strict_types=1);

use yii\web\View;

/**
 * @var View $this
 * @var array $data
 */
?>
<div>
    <?php foreach ($data as $row): ?>
        <div style="padding-bottom: 16px; <?= $row['correct'] ? '' : 'border: 1px red solid' ?>">
            <div style="display: flex; flex-direction: row; padding: 8px 0">
                <div style="flex: 2; font-weight: 700"><?= $row['question'] ?></div>
                <div style="flex: 1; display: flex; justify-content: center">Правильный ответ</div>
                <div style="flex: 1; display: flex; justify-content: center">Ответ пользователя</div>
            </div>

            <?php foreach ($row['answers'] as $answer): ?>
                <div style="display: flex; flex-direction: row; <?php echo $answer["correct"] === true ? 'background-color: rgba(25,135,84, 0.25)' : (in_array($answer["id"], $row["user_answers"]) ? 'background-color: rgba(220,53,69, 0.25)' : "")  ?>">
                    <div style="flex: 2; padding: 8px">
                        <p style="font-weight: 700; margin: 0; padding: 0;"><?= $answer["name"]; ?></p>
                    </div>
                    <div style="flex: 1; display: flex; justify-content: center; padding: 8px">
                        <?php if ($answer["correct"] === true): ?>
                            <span class="text-success"><i class="glyphicon glyphicon-ok-circle"></i></span>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1; display: flex; justify-content: center; padding: 8px">
                        <?php if (in_array($answer["id"], $row["user_answers"])): ?>
                            <span class="<?= $answer["correct"] === true ? "text-success" : "text-danger"; ?>"><i class="glyphicon glyphicon-ok"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
