<?php
use yii\helpers\Html;
/** @var $model backend\models\story_list\CreateStoryListForm|backend\models\story_list\UpdateStoryListForm */
$this->title = 'Редактирование списка историй';
$this->params['breadcrumbs'][] = ['label' => 'Списки историй', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-6">
        <div class="section-form">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>