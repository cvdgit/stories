<?php

declare(strict_types=1);

use common\models\UserStudent;
use yii\web\View;

/**
 * @var UserStudent $student
 * @var View $this
 */

$this->registerCss(<<<CSS
.student-toolbar__wrap {
    padding: 20px 0;
    margin-bottom: 20px;
}
.student-toolbar {
    display: flex;
    flex-direction: row;
    align-items: center;
}
CSS
);
?>
<div class="student-toolbar__wrap">
    <div class="student-toolbar">
        <div style="margin-right: auto"></div>
        <div>
            <?= $student->name . ' (' . $student->class->name . ')' ?>
        </div>
    </div>
</div>
