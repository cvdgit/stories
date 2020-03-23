<?php

use yii\helpers\Html;

/* @var $user common\models\User */

?>
<div>
    <p>Привет <?= Html::encode($user->username) ?>,</p>
    <p>Спасибо за регистрацию. Добро пожаловать на Wikids!</p>
    <p>___</p>
    <p>Популярные категории:</p>
    <div style="margin: 20px 0">
        <ul>
            <li><a target="_blank" href="https://wikids.ru/stories/category/drevnegrecheskie-mify">Древнегреческие мифы</a></li>
            <li><a target="_blank" href="https://wikids.ru/stories/category/russkie-skazki-i-byliny">Русские сказки и былины</a></li>
            <li><a target="_blank" href="https://wikids.ru/stories/category/poznavatelnye">Познавательные</a></li>
            <li><a target="_blank" href="https://wikids.ru/stories/category/altayskie-narodnye-skazki">Алтайские народные сказки</a></li>
        </ul>
    </div>
</div>
