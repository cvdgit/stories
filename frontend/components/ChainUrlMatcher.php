<?php

declare(strict_types=1);

namespace frontend\components;

class ChainUrlMatcher implements UrlMatcherInterface
{
    private $matchers;

    public function __construct(UrlMatcherInterface ...$matchers)
    {
        $this->matchers = $matchers;
    }

    public function match(string $url): ?array
    {
        foreach ($this->matchers as $matcher) {
            $result = $matcher->match($url);
            if ($result !== null) {
                return $result;
            }
        }
        return null;
    }
}
