<?php

declare(strict_types=1);

namespace frontend\TableOfContents;

use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserHistoryAction extends Action
{
    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function run(int $storyId, int $userId, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $data = (new TableOfContentsStatusFetcher())->fetch(
            $storyId,
            $userId,
        );
        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
