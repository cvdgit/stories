<?php

declare(strict_types=1);

use common\models\Story;
use yii\web\View;

/**
 * @var View $this
 * @var Story $model
 * @var string $configJSON
 */

$this->title = 'Редактор: ' . $model->title;
echo $this->render('_edit', ['model' => $model, 'configJSON' => $configJSON, 'inLesson' => false]);
