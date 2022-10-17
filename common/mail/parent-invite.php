<?php

declare(strict_types=1);

/**
 * @var int $code
 */
?>
<div>
    <p>Приглашение на Wikids!</p>
    <p>
        <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['/edu/parent/default/invite', 'code' => $code]) ?>">Принять приглашение</a>
    </p>
</div>
