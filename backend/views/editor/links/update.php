<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $this yii\web\View */
/** @var $model backend\models\links\UpdateLinkForm */
$this->title = 'Редактирование ссылки';
$this->params['sidebarMenuItems'] = [
    ['label' => 'Ссылки', 'url' => ['index', 'slide_id' => $model->getSlideID()]],
];
?>
<div>
    <div class="row">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php
            $form = ActiveForm::begin([
                'action' => ['update', 'id' => $model->link_id],
            ]);
            echo $form->field($model, 'title')->textInput();
            echo $form->field($model, 'href')->textInput();
            echo Html::submitButton('Изменить', ['class' => 'btn btn-primary']);
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
