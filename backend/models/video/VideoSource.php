<?php

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

    public static function asNavItems(int $source)
    {
        return array_map(static function(string $value, int $key) use ($source) {
            return [
                'label' => $value,
                'url' => ['video/index', 'source' => $key],
                'active' => ($source === $key),
            ];
        }, self::asArray(), array_keys(self::asArray()));
    }

}