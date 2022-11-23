<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use common\rbac\UserRoles;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStory;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StoryController extends Controller
{
    public $layout = '@frontend/views/layouts/edu';

/*    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::ROLE_STUDENT],
                    ],
                ],
            ],
        ];
    }*/

    /**
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionView(int $id, int $program_id): string
    {
        if (($story = EduStory::findOne($id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        if (($program = EduClassProgram::findOne($program_id)) === null) {
            throw new NotFoundHttpException('Программа не найдена');
        }

        $query = (new Query())
            ->select([
                'lessonId' => 'els.lesson_id',
            ])
            ->from(['els' => 'edu_lesson_story'])
            ->where(['els.story_id' => $story->id]);
        $rows = $query->all();
        if (count($rows) === 0) {
            throw new BadRequestHttpException('Не удалось определить урок');
        }

        $lessonId = $rows[0]['lessonId'];
        $backRoute = ['/edu/student/lesson', 'id' => $lessonId];

        return $this->render('view', [
            'story' => $story,
            'programId' => $program_id,
            'backRoute' => $backRoute,
        ]);
    }
}
