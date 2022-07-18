<?php
/* @var $this yii\web\View */

use modules\files\models\StudyFile;
use yii\helpers\Html;
use yii\helpers\Url;
$title = 'Перечень документов';
$this->setMetaTags($title,
    $title,
    '',
    $title);
$this->registerLinkTag(['rel' => 'canonical', 'href' => Url::canonical()]);
?>
<div class="container">
    <main style="margin-bottom:40px">
        <section style="padding:0">
            <h1 style="font-size:36px"><?= Html::encode($title) ?></h1>
        </section>
        <section style="padding:0">
            <ul class="list-unstyled">
                <li style="margin: 16px 0">
                    <a href="<?= StudyFile::getFileUrlByAlias('school_request') ?>">Заявление</a>
                </li>
                <li style="margin: 16px 0">
                    <a href="<?= StudyFile::getFileUrlByAlias('school_agree') ?>">Согласие на обработку персональных данных</a>
                </li>
                <li style="margin: 16px 0">
                    <a href="<?= StudyFile::getFileUrlByAlias('school_doc_list') ?>">Список документов для зачисления</a>
                </li>
            </ul>
        </section>
    </main>
</div>
