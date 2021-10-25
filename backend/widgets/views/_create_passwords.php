<?php
use yii\helpers\Html;
/** @var $groupId int */
?>
<?= Html::a('Сформировать пароли', ['study-group/create-passwords', 'group_id' => $groupId], ['class' => 'btn btn-primary btn-sm create-passwords']) ?>
<?php
$js = <<<JS
(function() {
    $('.create-passwords').on('click', function(e) {
        if (!confirm('Будут изменены пароли всех пользователей в группе. Продолжить?')) {
            e.preventDefault();
            return false;
        }
    });
})();
JS;
$this->registerJs($js);