<?php

declare(strict_types=1);

use modules\edu\models\EduClassBook;
use modules\edu\widgets\AdminToolbarWidget;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var EduClassBook $classBook
 */

$this->title = 'Класс';
?>
<div>
    <?= AdminToolbarWidget::widget() ?>

    <h1 class="h2 page-header">
        <?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/admin/class-book/index']) ?>
        <?= Html::encode($this->title) ?>
    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'columns' => [
            'name',
            [
                'attribute' => 'studentLogin.username',
                'label' => 'Логин',
            ],
            [
                'attribute' => 'studentLogin.password',
                'label' => 'Пароль',
            ],
            [
                'format' => 'raw',
                'value' => static function($model) {
                    return Html::a($model->user->profileName, '#');
                }
            ],
            [
                'format' => 'raw',
                'value' => static function($model) use ($classBook) {
                    return Html::a(
                        'Создать пользователя',
                        ['/edu/admin/class-book/create-user', 'class_book_id' => $classBook->id, 'student_id' => $model->id],
                        ['onclick' => "return confirm('Создать пользователя?')"]
                    );
                }
            ],
        ],
    ]) ?>
</div>
