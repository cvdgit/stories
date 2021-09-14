<?php
use frontend\widgets\StudyTaskRevealWidget;
/** @var $taskModel common\models\StudyTask */
echo StudyTaskRevealWidget::widget(['taskModel' => $taskModel]);