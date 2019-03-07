<?php

use yii\grid\GridView;

/* @var $this yii\web\View */

$this->title = 'Панель управления';
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
        	<div class="col-xs-6">
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
]); ?>
        	</div>
        </div>
    </div>
</div>
