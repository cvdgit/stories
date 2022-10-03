<?php

use common\models\UserStudent;
use modules\edu\widgets\StudentToolbarWidget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ListView;

/**
 * @var UserStudent $student
 * @var DataProviderInterface $dataProvider
 * @var View $this
 */

$this->title = $student->name;

$this->registerCss(<<<CSS
.header-block {
    display: flex;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    margin-top: 20px;
}
.panel-wrap {
    background-color: rgb(255, 255, 255);
    box-shadow: rgb(57 78 127 / 20%) 0px 4px 8px;
    border-radius: 16px;
    transition: transform 125ms ease-in-out 0s, box-shadow 125ms ease-in-out 0s;
}
.panel-wrap:hover {
    color: #091526d9;
}
.panel-img {
    width: 100%;
    height: 64px;
    margin-top: 32px;
    background-position: center center;
    background-size: auto 100%;
    background-repeat: no-repeat;
    background-image: url('/school/img/logo.svg');
}
.panel-inner {
    display: flex;
    flex-direction: column;
    position: relative;
    margin-top: 5px;
    width: 100%;
}
.panel-header {
    display: flex;
    flex-direction: column;
    height: 44px;
}
.panel-header__text {
    width: 100%;
    text-align: center;
    color: rgba(9, 21, 38, 0.85);
    padding: 0 14px;
    font-size: 18px;
    line-height: 24px;
    font-weight: bold;
}
.panel-progress {
    width: 177px;
    -webkit-box-align: center;
    align-items: center;
    margin: 0 auto;
    display: flex;
}
.progress-chart {
    width: 56px;
    height: 56px;
    margin-right: 12px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.progress-text {
    color: rgba(9, 21, 38, 0.6);
    font-size: 14px;
    line-height: 20px;
    transform: rotate(0.03deg);
}
CSS
);
?>
<div class="container">
    <?= StudentToolbarWidget::widget(['student' => $student]) ?>

    <div class="header-block">
        <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2">Обучение</h1>
    </div>

    <div style="margin-bottom: 40px">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'itemView' => '_program_item',
            'itemOptions' => ['tag' => false],
            'viewParams' => ['classId' => $student->class_id, 'studentId' => $student->id],
            'layout' => "{summary}\n<div class=\"row\">{items}</div>\n{pager}",
        ]) ?>
    </div>
</div>
