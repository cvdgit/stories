<?php

declare(strict_types=1);

use backend\models\UserUpdateForm;
use yii\bootstrap\Tabs;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var UserUpdateForm $model
 * @var DataProviderInterface $dataProvider
 * @var DataProviderInterface $historyDataProvider
 * @var DataProviderInterface $userStudentsDataProvider
 */

$this->title = 'Пользователь: ' . $model->getFio();
$this->params['sidebarMenuItems'] = [
	['label' => $model->username, 'url' => ['/user/update', 'id' => $model->id]],
];
$this->params['breadcrumbs'] = [
    ['label' => 'Все пользователи', 'url' => ['user/index']],
    $this->title,
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
                [
                    'label' => 'История ответов на вопросы',
                    'content' => $this->render('_user_question_history', ['dataProvider' => $userStudentsDataProvider]),
                ]
            ],
        ]) ?>
	</div>
</div>
