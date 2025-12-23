<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var string $storyTitle
 * @var string $studentName
 * @var string $date
 * @var array $historyData
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
    <h4 class="modal-title">История прохождения тестов</h4>
</div>
<div class="modal-body">
    <h4 style="font-weight: 600; margin-bottom: 20px;"><?= Html::encode($storyTitle) ?> (<?= Html::encode($studentName . ', ' . $date) ?>)</h4>
    <?php if (count($historyData) === 0): ?>
        <div>Нет данных</div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px">
            <?php foreach ($historyData as $row): ?>
                <div style="display: flex; flex-direction: row; align-items: start; gap: 10px">
                    <div style="white-space: nowrap; font-size: 16px; color: #909090"><?= $row['createdAt'] ?></div>
                    <div style="flex: 1; font-size: 18px; line-height: 26px">
                        <div style="margin-bottom: 10px"><b>Вопрос:</b> <?= $row['question'] ?></div>
                        <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center">
                            <div><b>Ответ:</b> <?= $row['answer'] ?></div>
                            <div style="width: 36px">
                                <?php if ($row['correct']): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 40 40">
                                        <path fill="#bae0bd" d="M20,38.5C9.8,38.5,1.5,30.2,1.5,20S9.8,1.5,20,1.5S38.5,9.8,38.5,20S30.2,38.5,20,38.5z"></path><path fill="#5e9c76" d="M20,2c9.9,0,18,8.1,18,18s-8.1,18-18,18S2,29.9,2,20S10.1,2,20,2 M20,1C9.5,1,1,9.5,1,20s8.5,19,19,19	s19-8.5,19-19S30.5,1,20,1L20,1z"></path><path fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="3" d="M11.2,20.1l5.8,5.8l13.2-13.2"></path>
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="30" height="30" viewBox="0 0 40 40">
                                        <path fill="#f78f8f" d="M20,38.5C9.799,38.5,1.5,30.201,1.5,20S9.799,1.5,20,1.5S38.5,9.799,38.5,20S30.201,38.5,20,38.5z"></path><path fill="#c74343" d="M20,2c9.925,0,18,8.075,18,18s-8.075,18-18,18S2,29.925,2,20S10.075,2,20,2 M20,1 C9.507,1,1,9.507,1,20s8.507,19,19,19s19-8.507,19-19S30.493,1,20,1L20,1z"></path><path fill="#fff" d="M18.5 10H21.5V30H18.5z" transform="rotate(-134.999 20 20)"></path><path fill="#fff" d="M18.5 10H21.5V30H18.5z" transform="rotate(-45.001 20 20)"></path>
                                    </svg>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif ?>
</div>
