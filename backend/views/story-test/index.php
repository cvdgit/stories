<?php

declare(strict_types=1);

use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var array $sidebarMenuItems
 * @var array $breadcrumbs
 * @var int $storyId
 * @var string $title
 */

$this->params = array_merge($this->params, $sidebarMenuItems);
$this->params = array_merge($this->params, $breadcrumbs);
$this->title = $title;
$this->registerJs($this->renderFile('@backend/views/story-test/index.js'));
?>
<div>
    <h1 class="page-header">Тесты из истории</h1>
</div>
<div style="padding: 20px 0">
    <a id="update-test-repeat" href="<?= Url::to(['/story-test/update-repeat-form', 'id' => $storyId]); ?>" class="btn btn-primary">Изменить кол-во повторений вопросов</a>
    <a id="update-pass-test-repeat" href="<?= Url::to(['/story-test/update-pass-test-repeat-form', 'id' => $storyId]); ?>" class="btn btn-primary">Изменить значение "Возврат на" в вопросах с пропусками</a>
</div>
<div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            "id",
            [
                "attribute" => "title",
                "format" => "raw",
                "label" => "Название",
                "value" => static function(array $model): string {
                    return Html::a($model["title"], ["/test/update", "id" => $model["id"]], ["target" => "_blank"]);
                }
            ],
            "header:ntext:Заголовок",
            "description_text:ntext:Описание",
            "created_at:datetime:Создан"
        ],
    ]); ?>
</div>
