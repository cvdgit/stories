<?php

namespace backend\components;

use backend\components\book\BlockCollection;
use yii\helpers\Html;

class BlockRenderer
{

    public static function renderCollection(BlockCollection $collection, $content)
    {
        $html = '';
        foreach ($collection as $item) {
            if (!$item->isEmpty()) {
                $html .= $content($item);
            }
        }
        return $html;
    }

    public static function renderImages(BlockCollection $images)
    {
        return self::renderCollection($images, function($item) {
            return Html::img(null, ['data-src' => $item->image, 'width' => '100%', 'height' => '100%', 'class' => 'lazy']);
        });
    }

    public static function renderTexts(BlockCollection $texts)
    {
        return self::renderCollection($texts, function($item) {
            return Html::tag('p', $item->text);
        });
    }

    public static function renderVideos(BlockCollection $videos)
    {
        return self::renderCollection($videos, function($item) {
            $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']);
            $text = Html::tag('p', 'Содержимое доступно в режиме обучения');
            return Html::tag('div', $icon . $text, ['class' => 'guest-story-video to-slides-tab']);
        });
    }

}