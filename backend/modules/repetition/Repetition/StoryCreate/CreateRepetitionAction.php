<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\StoryCreate;

use backend\modules\repetition\Repetition\TestingCreate\CreateRepetitionForm;
use backend\modules\repetition\Repetition\UserStudentItemsFetcher;
use backend\modules\repetition\Schedule\Schedule;
use common\models\Story;
use common\models\UserStudent;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class CreateRepetitionAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $story_id, Request $request, Response $response)
    {
        $story = Story::findOne($story_id);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $createForm = new CreateRepetitionForm();

        if ($createForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;

            if (!$createForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }


        }

        return $this->controller->renderAjax('create', [
            'formModel' => $createForm,
            'studentItems' => ArrayHelper::map((new UserStudentItemsFetcher())->fetch(), 'studentId', 'studentName'),
            'scheduleItems' => ArrayHelper::map(Schedule::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
            'storyTests' => (new StoryTestsFetcher())->fetch($story->id),
        ]);
    }
}
