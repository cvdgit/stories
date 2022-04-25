<?php
/** @var yii\web\View $this */
/** @var common\models\Story $model */
/** @var string $configJSON */
$this->title = 'Редактор: ' . $model->title;
?>
<?= $this->render('_edit', ['model' => $model, 'configJSON' => $configJSON, 'inLesson' => false]) ?>
