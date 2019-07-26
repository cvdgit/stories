<?php

use yii\bootstrap\Tabs;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $historyDataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователь: ' . $model->username;
$this->params['sidebarMenuItems'] = [
	['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
];
?>
<div class="row">
	<div class="col-xs-12">
		<h2 class="page-header"><?= Html::encode($this->title) ?></h2>
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Пользователь',
                    'content' => $this->render('_update_tab', ['model' => $model]),
                    'active' => true,
                ],
                [
                    'label' => 'Подписки',
                    'content' => $this->render('_subscriptions_tab', ['model' => $model, 'dataProvider' => $dataProvider]),
                ],
                [
                    'label' => 'Просмотры историй',
                    'content' => $this->render('_story_history', ['model' => $model, 'dataProvider' => $historyDataProvider]),
                ],
            ],
        ]) ?>
	</div>
</div>
