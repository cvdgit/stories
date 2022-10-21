<?php

declare(strict_types=1);

namespace frontend\components;

class StoryUrlMatcher implements UrlMatcherInterface
{
    public function match(string $url): ?array
    {
        $urlParams = parse_url($url);
        $matches = [];
        if (preg_match('/^\/story\/([\w\-]+)/', $urlParams['path'], $matches)) {
            return [
                'field' => 'alias',
                'value' => $matches[1],
            ];
        }
        return null;
    }
}
