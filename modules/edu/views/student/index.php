<?php

use common\models\UserStudent;
use modules\edu\models\EduClassBook;
use modules\edu\widgets\StudentToolbarWidget;
use yii\data\DataProviderInterface;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var UserStudent $student
 * @var DataProviderInterface $dataProvider
 * @var EduClassBook $classBook
 * @var View $this
 */

$this->title = $student->name;
?>
<div class="container">
    <?= StudentToolbarWidget::widget(['student' => $student]) ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'itemView' => '_program_item',
        'itemOptions' => ['tag' => false],
        'viewParams' => ['classId' => $classBook->class_id],
        'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
    ]) ?>
</div>
