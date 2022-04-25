<?php
/** @var yii\web\View $this */
/** @var common\models\Story $model */
/** @var string $configJSON */
/** @var common\models\Lesson $lesson */
$this->title = 'Редактор: ' . $model->title;
?>
<div class="container-fluid" style="padding: 0">
    <div class="row">
        <div class="col-lg-12">
            <div class="lesson-bar">
                <div class="bar-back">
                    <button class="button" type="button" onclick="location.href='/admin/index.php?r=course/update&id=<?= $model->id ?>'">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                    </button>
                </div>
                <div class="bar-content">
                    <h3 class="back-content__title"><?= $lesson->name ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_edit', ['model' => $model, 'configJSON' => $configJSON, 'inLesson' => true]) ?>
