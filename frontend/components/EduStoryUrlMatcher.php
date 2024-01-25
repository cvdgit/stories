<?php

declare(strict_types=1);

namespace frontend\components;

class EduStoryUrlMatcher implements UrlMatcherInterface
{
    public function match(string $url): ?array
    {
        $urlParams = parse_url($url);

        if (!isset($urlParams["path"])) {
            return null;
        }

        $matches = [];
        if (preg_match('/^\/edu\/story\/(\d+)/', $urlParams['path'], $matches)) {
            return [
                'field' => 'id',
                'value' => $matches[1],
            ];
        }
        return null;
    }
}
