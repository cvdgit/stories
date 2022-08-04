<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\edu\models\EduTopic */

$this->title = 'Создать тему';
$this->params['breadcrumbs'][] = ['label' => 'Темы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-8">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
