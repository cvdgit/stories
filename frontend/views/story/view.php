<?php

/* @var $this yii\web\View */
/* @var $model common\models\Story */

use frontend\widgets\RevealWidget;

echo RevealWidget::widget(['data' => $model->body]);
?>
