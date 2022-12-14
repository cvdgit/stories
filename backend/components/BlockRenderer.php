<?php

declare(strict_types=1);

namespace backend\components;

use backend\components\book\BlockCollection;
use backend\components\book\blocks\Image;
use backend\components\book\blocks\Link;
use backend\components\book\blocks\Test;
use backend\components\book\blocks\Text;
use backend\components\book\blocks\Video;
use yii\helpers\Html;

class BlockRenderer
{
    public static function renderCollection(BlockCollection $collection, $content): string
    {
        $html = '';
        foreach ($collection as $item) {
            if (!$item->isEmpty()) {
                $html .= $content($item);
            }
        }
        return $html;
    }

    /**
     * @param BlockCollection<Image> $images
     * @return string
     */
    public static function renderImages(BlockCollection $images): string
    {
        return self::renderCollection($images, static function(Image $item) {
            return Html::img(null, ['data-src' => $item->getImage(), 'style' => 'max-width: 100%; margin: 20px auto', 'class' => 'lazy']);
        });
    }

    /**
     * @param BlockCollection<Text> $texts
     * @return string
     */
    public static function renderTexts(BlockCollection $texts): string
    {
        return self::renderCollection($texts, static function(Text $item) {
            return Html::tag('p', $item->getText());
        });
    }

    /**
     * @param BlockCollection<Video> $videos
     * @return string
     */
    public static function renderVideos(BlockCollection $videos): string
    {
        return self::renderCollection($videos, static function($item) {
            $icon = Html::tag('span', '', ['class' => 'glyphicon glyphicon-film']);
            $text = Html::tag('p', 'Содержимое доступно в режиме обучения');
            return Html::tag('div', $icon . $text, ['class' => 'guest-story-video to-slides-tab']);
        });
    }

    /**
     * @param BlockCollection<Test> $tests
     * @return string
     */
    public static function renderTests(BlockCollection $tests): string
    {
        return self::renderCollection($tests, static function(Test $test) {
            $contents[] = Html::tag('h3', $test->getHeader());
            $contents[] = Html::tag('p', $test->getDescription());
            $contents[] = Html::tag(
                'div',
                Html::tag('div', '', [
                    'data-toggle' => 'mobile-testing',
                    'class' => 'new-questions',
                    'data-test-id' => $test->getTestId(),
                    'data-guest-mode' => '1',
                ]),
                ['class' => 'alert noselect text-center']
            );
            return Html::tag('div', implode("\n", $contents));
        });
    }

    /**
     * @param BlockCollection<Link> $links
     * @return string
     */
    public static function renderLinks(BlockCollection $links): string
    {
        $contents[] = Html::tag('h3', 'Полезные ссылки');
        $content = self::renderCollection($links, static function(Link $link) {
            return Html::tag('li', Html::a($link->getTitle(), $link->getHref(), ['rel' => 'nofollow', 'target' => '_blank']));
        });
        $contents[] = Html::tag('ul', $content, ['class' => 'list-inline']);
        return implode("\n", $contents);
    }
}
