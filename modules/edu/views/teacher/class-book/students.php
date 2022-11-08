<?php

declare(strict_types=1);

use backend\widgets\grid\PjaxDeleteButton;
use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\widgets\TeacherMenuWidget;
use yii\data\DataProviderInterface;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var DataProviderInterface $dataProvider
 * @var ClassBookForm $formModel
 */

$this->title = 'Редактирование класса';

$this->registerCss(<<<CSS
.header-block {
    display: flex;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    margin-top: 20px;
}
CSS
);
?>
<div class="container">
    <?= TeacherMenuWidget::widget() ?>

    <div class="header-block">
        <div style="display: flex; flex-direction: row; align-items: center">
            <h1 style="font-size: 32px; margin: 0; font-weight: 500; line-height: 1.2" class="h2"><?= Html::a('<i class="glyphicon glyphicon-arrow-left back-arrow"></i>', ['/edu/teacher/class-book/index']) ?> <?= Html::encode($this->title) ?></h1>
            <div style="margin-left: 1rem; margin-right: auto; height: 100%">
                <a href="<?= Url::to(['/edu/teacher/class-book/update', 'id' => $formModel->getId()]) ?>">Изменить</a>
            </div>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <?= Html::a('Добавить ученика', ['/edu/teacher/class-book/create-student', 'id' => $formModel->getId()], ['class' => 'btn btn-small']) ?>
            </div>
        </div>
    </div>

    <div>
        <p class="lead">Класс: <?= Html::encode($formModel->name) ?></p>
    </div>

    <?php Pjax::begin(['id' => 'pjax-students']); ?>
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
                'label' => 'Родитель',
                'format' => 'raw',
                'value' => static function($model) {
                    if ($model->haveInvitedParent()) {
                        return 'На Wikids';
                    }
                    return Html::a('Пригласить', ['/edu/teacher/class-book/parent-invite', 'student_id' => $model->id], [
                        'data-toggle' => 'modal',
                        'data-target' => '#parent-invite-modal',
                    ]);
                }
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{delete}',
                'buttons' => [
                    'delete' => static function($url, $model) {
                        return new PjaxDeleteButton('#', [
                            'class' => 'pjax-delete-link',
                            'delete-url' => Url::to(['/edu/teacher/class-book/delete-student', 'id' => $model->id]),
                            'pjax-container' => 'pjax-students',
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<div class="modal site-dialog remote fade" tabindex="-1" id="parent-invite-modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
(function() {
    const modal = $('#parent-invite-modal');
    modal
        .on('hide.bs.modal', function() {
            $(this).removeData('bs.modal');
            $(this).find('.modal-content').html('');
        })
        .on('loaded.bs.modal', function() {
            const formElement = $('#parent-invite-form', this);
            onBeforeSubmitForm(formElement, (form) => {
                const formData = new FormData(form);
                sendForm(formData, $(form).attr('action'), $(form).attr('method'))
                    .done(response => {
                        if (response && response.success) {
                            toastr.success('Приглашение успешно отправлено');
                        }
                        if (response && response.success === false) {
                            toastr.error(response.message);
                        }
                    })
                    .always(() => modal.modal('hide'));
            });
        });
})();
JS
);
