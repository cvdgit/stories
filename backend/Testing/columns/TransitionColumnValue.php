<?php

declare(strict_types=1);

namespace backend\Testing\columns;

use common\models\StoryTest;
use yii\bootstrap\Html;

class TransitionColumnValue
{
    public function __invoke(): \Closure
    {
        return static function(StoryTest $model) {
            $html = '';
            $stories = $model->stories;
            if (count($stories) > 0) {
                $story = $stories[0];
                $html = Html::a('к истории', \Yii::$app->urlManagerFrontend->createAbsoluteUrl(['story/view', 'alias' => $story->alias]), ['data-pjax' => '0', 'target' => '_blank']);
            }
            if ($model->haveWordList()) {
                if (!empty($html)) {
                    $html .= '<br/>';
                }
                $html .= Html::a('к списку слов', \common\models\TestWordList::getUpdateUrl($model->word_list_id), ['data-pjax' => '0', 'target' => '_blank']);
            }
            return $html;
        };
    }
}
