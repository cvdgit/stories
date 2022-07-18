<?php
use yii\helpers\Html;
/**
 * @var $this yii\web\View
 * @var $model modules\files\models\StudyFolder
 */
$this->title = 'Создание папки';
$this->params['breadcrumbs'][] = ['label' => 'Папки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= Html::encode($this->title) ?></h1>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="study-folder-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
