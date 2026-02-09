<?php

declare(strict_types=1);

namespace frontend\TableOfContents;

use common\models\UserStudent;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\User as WebUser;

class UserHistoryAction extends Action
{
    /**
     * @throws InvalidConfigException|NotFoundHttpException|BadRequestHttpException
     */
    public function run(int $storyId, int $userId, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $student = UserStudent::findMainByUserId($user->getId());
        if ($student === null) {
            throw new BadRequestHttpException('Student not found');
        }

        $data = (new TableOfContentsStatusFetcher())->fetch(
            $storyId,
            $user->getId(),
            $student->id,
        );
        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
