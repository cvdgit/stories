<?php

declare(strict_types=1);

namespace frontend\TableOfContents;

use common\models\UserStudent;
use frontend\SpeechTrainer\SpeechTrainerContentsFetcher;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class UserHistoryAction extends Action
{
    /**
     * @throws InvalidConfigException|NotFoundHttpException|BadRequestHttpException
     */
    public function run(int $storyId, int $userId, Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $student = UserStudent::findMainByUserId($user->getId());
        if ($student === null) {
            throw new BadRequestHttpException('Student not found');
        }

        $isEducation = false;
        if ($request->referrer !== null) {
            $path = parse_url($request->referrer, PHP_URL_PATH);
            $isEducation = preg_match('#^/edu/#', $path) === 1;
        }

        $data = (new TableOfContentsStatusFetcher(
            new SpeechTrainerContentsFetcher()
        ))->fetch(
            $storyId,
            $user->getId(),
            $student->id,
            $isEducation
        );
        return [
            'success' => true,
            'data' => $data,
        ];
    }
}
