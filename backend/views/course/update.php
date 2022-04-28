<?php
use backend\assets\CourseAsset;
use backend\assets\WikidsRevealAsset;
use yii\web\View;
use yii\helpers\Url;
/** @var common\models\Story $storyModel */
/** @var yii\web\View $this */
/** @var string $course */
/** @var bool $haveLessons */
/** @var bool $haveSlides */
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
            <div class="col-lg-12">
                <div style="padding-bottom: 10px; padding-top: 10px; border-bottom: 1px solid #e5e5e5; display: flex; flex-direction: row; align-items: center">
                    <nav>
                        <ul class="nav nav-pills">
                            <li role="presentation"><a href="<?= Url::to(['story/update', 'id' => $storyModel->id]) ?>">История</a></li>
                        </ul>
                    </nav>
                    <div style="margin-left: auto">
                        <?php if (!$haveLessons && $haveSlides): ?>
                        <a href="<?= Url::to(['course/create-from-slides', 'id' => $storyModel->id]) ?>" class="btn btn-primary">Создать курс на основе слайдов</a>
                        <?php endif ?>
                        <!--button id="save-course" class="btn btn-primary">Сохранить курс</button-->
                        <?php if ($haveLessons): ?>
                        <a href="<?= Url::to(['course/delete', 'id' => $storyModel->id]) ?>" class="btn btn-danger">Удалить курс</a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
