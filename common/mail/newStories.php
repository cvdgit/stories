<?php
/** @var $stories common\models\Story[] */
?>
<center style="width:100%;background-color:#ffffff">
    <div style="width:600px;margin:0 auto;padding:0">
        <table cellspaceing="0" cellpadding="0" border="0" width="600" style="border-width:0;border-collapse:collapse;border-spacing:0;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;font-size:18px;font-weight:400;line-height:normal;color:#444444;width:600px!important">
            <tbody><tr><td>
                <p style="color:#444444;font-size:18px;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;line-height:24px;margin:10px 0 18px 0;padding:0">Новые истории на Wikids</p>
            </td></tr></tbody>
        </table>
        <?php foreach ($stories as $story): ?>
        <?= $this->render('newStory-html', ['story' => $story]) ?>
        <?php endforeach ?>
    </div>
</center>
