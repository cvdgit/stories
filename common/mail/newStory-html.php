<?php

use common\components\StoryCover;
use yii\helpers\Html;

/** @var $story common\models\Story */

?>
<div>
    <p>Здравствуйте, {{Name}}</p>
    <p>На Wikids добавлена новая история - <?= Html::a($story->title, Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/story/view', 'alias' => $story->alias])) ?></p>
    <table>
        <tbody>
            <tr>
                <td>
                    <?= Html::img(StoryCover::getListThumbPath($story->cover)) ?>
                </td>
                <td>
                    <?= $story->description ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
