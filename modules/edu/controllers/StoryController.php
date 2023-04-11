<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use common\rbac\UserRoles;
use modules\edu\components\TopicAccessManager;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStory;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StoryController extends Controller
{
    public $layout = '@frontend/views/layouts/edu';

    private $accessManager;

    public function __construct($id, $module, TopicAccessManager $accessManager, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->accessManager = $accessManager;
    }

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
     * @throws ForbiddenHttpException
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
            ->innerJoin(['l' => 'edu_lesson'], 'els.lesson_id = l.id')
            ->innerJoin(['t' => 'edu_topic'], 'l.topic_id = t.id')
            ->where([
                'els.story_id' => $story->id,
                't.class_program_id' => $program->id,
            ]);
        $rows = $query->all();
        if (count($rows) === 0) {
            throw new BadRequestHttpException('Не удалось определить урок');
        }

        $lessonId = $rows[0]['lessonId'];
        $backRoute = ['/edu/student/lesson', 'id' => $lessonId];

        $studentId = Yii::$app->studentContext->getId();
        if ($studentId === null) {
            $studentId = Yii::$app->user->identity->getStudentID();
        }

        if (!Yii::$app->user->can(UserRoles::ROLE_TEACHER)) {
            $this->accessManager->checkAccessLesson($program->id, (int)$lessonId, $studentId);
        }

        return $this->render('view', [
            'story' => $story,
            'programId' => $program_id,
            'backRoute' => $backRoute,
            'studentId' => $studentId,
        ]);
    }
}
