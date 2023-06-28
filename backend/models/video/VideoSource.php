<?php

declare(strict_types=1);

namespace backend\models\video;

use common\models\SlideVideo;

class VideoSource
{
    public const YOUTUBE = 1;
    public const FILE = 2;

    public static function getTypes(): array
    {
        return [self::YOUTUBE, self::FILE];
    }

    public static function asArray(): array
    {
        return [
            self::YOUTUBE => 'Видео с YouTube',
            self::FILE => 'Видео из файлов',
        ];
    }

    public static function isFile(SlideVideo $model): bool
    {
        return (int) $model->source === self::FILE;
    }

    public static function isYouTube(SlideVideo $model): bool
    {
        return (int) $model->source === self::YOUTUBE;
    }

    public static function asNavItems(): array
    {
        $items = self::asArray();
        return [
            [
                'label' => $items[self::YOUTUBE],
                'url' => ['video/index'],
            ],
            [
                'label' => $items[self::FILE],
                'url' => ['video/file/index'],
            ],
        ];
    }
}
