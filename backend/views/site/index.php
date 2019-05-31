<?php

use backend\helpers\SummaryHelper;
use yii\grid\GridView;

/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Панель управления';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
            <div class="col-xs-3">
                <h4>Сегодня</h4>
                <ul class="list-group">
                    <li class="list-group-item">Активировано подписок <span class="badge"><?= SummaryHelper::activatedSubscriptions() ?></span></li>
                    <li class="list-group-item">Опубликовано историй <span class="badge"><?= SummaryHelper::publishedStories() ?></span></li>
                    <li class="list-group-item">Зарегистрировано пользователей <span class="badge"><?= SummaryHelper::registeredUsers() ?></span></li>
                    <li class="list-group-item">Написано комментариев <span class="badge"><?= SummaryHelper::commentsWritten() ?></span></li>
                </ul>
            </div>
        	<div class="col-xs-6 col-xs-offset-3">
        		<h4>Количество просмотров историй с % завершения</h4>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'summary' => false,
                    'columns' => [
                        [
                            'attribute' => 'title',
                            'label' => 'История',
                        ],
                        [
                            'attribute' => 'views_number',
                            'label' => 'Количество просмотров',
                        ],
                        [
                            'attribute' => 'story_done',
                            'label' => '% завершенных просмотров',
                        ],
                    ],
                ]) ?>
        	</div>
        </div>
    </div>
</div>
