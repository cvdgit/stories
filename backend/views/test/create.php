<?php

declare(strict_types=1);

use common\models\StoryTest;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var StoryTest $model
 * @var array $scheduleItems
 */

$this->title = 'Создать тест';
$this->params['breadcrumbs'] = [
    ['label' => 'Все тесты', 'url' => ['test/index', 'source' => $model->source]],
    $this->title,
];
?>
<div class="story-test-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
                'repeatChangeModel' => null,
                'scheduleItems' => $scheduleItems
            ]) ?>
        </div>
    </div>
</div>
