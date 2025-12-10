<?php

declare(strict_types=1);

use backend\models\StoryCoverUploadForm;
use backend\models\StoryFileUploadForm;
use common\models\Story;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Story $model
 * @var StoryCoverUploadForm $coverUploadForm
 * @var StoryFileUploadForm $fileUploadForm
 */

$this->title = 'Создание истории';
$this->params['breadcrumbs'] = [
    ['label' => 'Список историй', 'url' => ['index']],
    $this->title,
];
?>
<div class="row">
	<div class="col-xs-6">
		<h1 class="page-header"><?= Html::encode($this->title) ?></h1>
		<?= $this->render('_form', [
		    'model' => $model,
		    'coverUploadForm' => $coverUploadForm,
		    'fileUploadForm' => $fileUploadForm,
            'isNew' => true,
		]) ?>
	</div>
	<div class="col-xs-6"></div>
</div>
