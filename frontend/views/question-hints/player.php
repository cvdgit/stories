<?php
use common\rbac\UserRoles;
use frontend\widgets\HintsRevealWidget;
use yii\helpers\Json;
?>
<script>
    var WikidsConfig = {
        'user': {
            'isGuest': <?= Json::encode(Yii::$app->user->isGuest) ?>,
            'isModerator': <?= Json::encode(Yii::$app->user->can(UserRoles::ROLE_MODERATOR)) ?>
        }
    };
</script>
<div class="story-head-container">
    <main class="site-story-main">
        <div class="story-container">
            <div class="story-container-inner" id="story-container">
                <?= HintsRevealWidget::widget(['model' => $model, 'data' => $data]) ?>
            </div>
        </div>
    </main>
</div>