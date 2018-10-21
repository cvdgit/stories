<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
<h1>Профиль пользователя</h1>
 <?php echo DetailView::widget([
     'model' => $model,
     'attributes' => [
         'username',
         'email',
         'status',
         'group',
         'created_at:datetime', // creation date formatted as datetime
     ],
]); ?>
</div>