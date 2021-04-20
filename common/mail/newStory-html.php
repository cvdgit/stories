<?php
use common\components\StoryCover;
use yii\helpers\Html;
/** @var $story common\models\Story */
?>
<table cellspaceing="0" cellpadding="0" border="0" width="600" align="center" style="border-spacing:0;border:0;border-collapse:collapse;font-family:sans-serif;color:#444444;Margin:0 auto 20px auto;width:600px;max-width:600px;min-width:600px">
    <tbody>
    <tr>
        <td width="600" style="padding:0;text-align:left;font-size:0;max-width:600px">
            <table cellspaceing="0" cellpadding="0" border="0" width="100%">
                <tbody>
                <tr>
                    <td style="border:2px solid #f1f3f4">
                        <table cellspaceing="0" cellpadding="0" border="0" width="100%">
                            <tbody>
                            <tr>
                                <td>
                                    <table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" style="border-width:0;border-collapse:collapse;border-spacing:0;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;font-size:12px;font-weight:400;line-height:normal;background-color:#ffffff;background-image:none;background-repeat:repeat;background-position:top left;color:#ffffff;direction:LTR" dir="LTR">
                                        <tbody>
                                        <tr style="border-width:0">
                                            <td valign="top" align="right" style="border-width:0;color:#3c4043;font-family:Google Sans,Roboto,Helvetica,Arial,sans-serif;font-size:30px;font-weight:400;line-height:42px;padding-bottom:5px;text-align:center;padding-left:20px;padding-right:20px;padding-top:10px;background-color:#ffffff;background-image:none;background-repeat:repeat;background-position:top left;word-break:normal"><?= $story->title ?></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" style="border:0;border-collapse:collapse;border-spacing:0;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;font-size:21px;font-weight:400;width:474px!important;line-height:21px" width="474">
                                        <tbody>
                                        <tr style="border:0">
                                            <td style="border:0;color:#444444;padding:0 20px 5px 20px">
                                                <table border="0" cellpadding="0" cellspacing="0" style="border:0;border-collapse:collapse;border-spacing:0;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;font-weight:400;line-height:21px" width="100%">
                                                    <tbody>
                                                    <tr>
                                                        <td style="margin:0;padding:0;text-align:center" bgcolor="#ffffff">
                                                            <?= Html::img(Yii::$app->urlManagerFrontend->baseUrl . StoryCover::getListThumbPath($story->cover)) ?>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table cellspaceing="0" cellpadding="0" border="0" width="474" style="border-width:0;border-collapse:collapse;border-spacing:0;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;font-size:18px;font-weight:400;line-height:normal;color:#444444;width:474px!important">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <p style="color:#444444;font-size:18px;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif;line-height:24px;margin:10px 0 18px 0;padding:0"><?= $story->description ?></p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table style="background-color:#ffffff;border-collapse:collapse;color:#444444;margin:0 auto;max-width:474px!important;min-width:474px!important;padding:0;table-layout:fixed;width:474px!important" bgcolor="#ffffff" width="474">
                                                    <tbody><tr>
                                                        <td align="center" width="434" style="border-collapse:collapse;padding:24px 20px 20px 20px;width:434px!important" dir="LTR">
                                                            <div width="434" style="display:block;width:434px!important">
                                                                <a href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/story/view', 'alias' => $story->alias]) ?>" style="background-color:#4285f4;border:1px solid;border-radius:2px;color:#ffffff;display:inline-block;font-size:18px;font-weight:normal;line-height:38px;padding:6px 0 6px 0;text-align:center;text-decoration:none;text-transform:uppercase;white-space:nowrap;width:100%;font-family:Roboto,Arial,Helvetica Neue,Helvetica,sans-serif" bgcolor="#4285F4" align="center" width="100%" target="_blank">ПЕРЕЙТИ К ИСТОРИИ</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    </tbody></table>
                                            </td></tr></tbody></table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
