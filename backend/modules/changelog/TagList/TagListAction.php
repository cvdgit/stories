<?php

declare(strict_types=1);

namespace backend\modules\changelog\TagList;

use yii\base\Action;
use yii\web\Response;

class TagListAction extends Action
{
    public function run(string $query, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        return (new TagListSearch())->search(['query' => $query]);
    }
}
