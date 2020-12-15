<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
/** @var $this yii\web\View */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $students array */
/** @var $activeStudent common\models\UserStudent */
/** @var $category common\models\Category */
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container">
    <div class="row">
        <nav class="col-xs-12 col-sm-12 col-md-12 col-lg-3 site-sidebar">
            <h3>Ученики</h3>
            <div class="list-group">
                <?php foreach ($students as $student): ?>
                <?php $active = $student['id'] === $activeStudent->id ? ' active' : '' ?>
                <?= Html::a($student['name'], ['test/index', 'category_id' => $category->id, 'student_id' => $student['id']], ['class' => 'list-group-item' . $active]) ?>
                <?php endforeach ?>
            </div>
        </nav>
        <main class="col-xs-12 col-sm-12 col-md-12 col-lg-9 site-main" style="margin-top: 0">
            <h1 style="margin-top: 0; margin-bottom: 20px"><?= Html::a($category->name, $category->getCategoryUrl()) ?> / <?= $this->getHeader() ?></h1>
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_item',
                'viewParams' => [
                    'student' => $activeStudent,
                    'category' => $category,
                ],
            ]) ?>
        </main>
    </div>
</div>
