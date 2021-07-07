<?php

namespace backend\components\story;

class BlockType
{

    private const TYPE_TEXT = 'text';
    private const TYPE_HEADER = 'header';
    private const TYPE_IMAGE = 'image';
    private const TYPE_BUTTON = 'button';
    private const TYPE_TRANSITION = 'transition';
    private const TYPE_TEST = 'test';
    private const TYPE_HTML = 'html';
    private const TYPE_VIDEO = 'video';
    private const TYPE_VIDEOFILE = 'videofile';

    public static function isHtml(AbstractBlock $block): bool
    {
        return $block->getType() === self::TYPE_HTML;
    }

    public static function isVideo(AbstractBlock $block): bool
    {
        return $block->getType() === self::TYPE_VIDEO;
    }

    public static function isVideoFile(AbstractBlock $block): bool
    {
        return $block->getType() === self::TYPE_VIDEOFILE;
    }
}
