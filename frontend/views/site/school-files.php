<?php
/* @var $this yii\web\View */

use modules\files\models\StudyFile;
use yii\helpers\Html;
use yii\helpers\Url;
$title = 'Список документов для зачисления';
$this->setMetaTags($title,
    $title,
    '',
    $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container">
    <main style="margin-bottom:40px">
        <section style="padding:0;margin-bottom:30px">
            <h1 style="font-size:36px"><?= Html::encode($title) ?></h1>
        </section>
        <section style="padding:0;line-height:1.8">
            <ul>
                <li style="margin-bottom:20px">Заявление на имя директора ОАНО СОШ «Пенаты» от родителя/законного представителя о зачислении ребёнка в качестве экстерна для прохождения промежуточной аттестации за курс соответствующего класса (скан-копия). <a style="color:#007bff" href="<?= StudyFile::getFileUrlByAlias('school_request') ?>">Бланк прилагается</a></li>
                <li style="margin-bottom:20px">Свидетельство о рождении (скан-копия).</li>
                <li style="margin-bottom:20px">Согласие на обработку персональных данных (скан-копия). <a style="color:#007bff" href="<?= StudyFile::getFileUrlByAlias('school_agree') ?>">Бланк прилагается</a></li>
                <li style="margin-bottom:20px">Личное дело ученика, которое при переходе на семейный формат образования необходимо забрать из школы. Личное дело предоставляется для обучающихся 2-4 классов; для обучающихся 1-го класса личное дело будет заведено ОАНО СОШ «Пенаты». (Перед зачислением направляете нам скан-копию личного дела. Оригинал направляется по почте и хранится в ОАНО СОШ «Пенаты».)</li>
            </ul>
        </section>
    </main>
</div>
