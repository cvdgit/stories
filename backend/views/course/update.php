<?php
use backend\assets\CourseAsset;
use backend\assets\WikidsRevealAsset;
use yii\web\View;
/** @var common\models\Story $storyModel */
/** @var yii\web\View $this */
/** @var string $course */
CourseAsset::register($this);
WikidsRevealAsset::register($this);
$js = <<<JS
window['courseData'] = $course;
JS;
$this->registerJs($js, View::POS_END);
$this->title = 'Разделы - ' . $storyModel->title;
?>
<div class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div style="margin: 30px 0">
                    <div>
                        <h1><?= $storyModel->title ?></h1>
                    </div>
                    <div>
                        <?= $storyModel->description ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div id="app">
                    <div class="page-loader">
                        <div class="page-loader-inner">
                            <div class="page-loader-spinner"></div>
                            <p class="page-loader-message">Загрузка...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="text-center" style="margin-top: 20px;">
                    <button id="save-course" class="btn btn-primary hide">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal remote fade" id="create-block-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal remote fade" id="update-block-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
