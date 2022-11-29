<?php

declare(strict_types=1);

use backend\forms\WordListForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var WordListForm $model
 */

$this->title = 'Создать список слов';
$this->params['breadcrumbs'][] = ['label' => 'Списки слов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-word-list-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]); ?>
        </div>
    </div>
</div>
