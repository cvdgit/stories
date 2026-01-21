<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use common\rbac\UserRoles;
use frontend\MentalMap\Content\ContentMentalMapsFetcher;
use modules\edu\components\TopicAccessManager;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStory;
use modules\edu\models\EduStorySlide;
use modules\edu\query\StudentClassFetcher;
use Yii;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\User as WebUser;

class StoryController extends Controller
{
    public $layout = '@frontend/views/layouts/edu';

    private $accessManager;
    private $studentClassFetcher;

    public function __construct($id, $module, TopicAccessManager $accessManager, StudentClassFetcher $studentClassFetcher, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->accessManager = $accessManager;
        $this->studentClassFetcher = $studentClassFetcher;
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
    public function actionView(int $id, int $program_id, WebUser $user): string
    {
        if (($story = EduStory::findOne($id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }
        if (($program = EduClassProgram::findOne($program_id)) === null) {
            throw new NotFoundHttpException('Программа не найдена');
        }

        $studentId = Yii::$app->studentContext->getId();
        if ($studentId === null) {
            $studentId = Yii::$app->user->identity->getStudentID();
        }
        if ($studentId === null) {
            throw new ForbiddenHttpException('Доступ запрещен (ученик не определен)');
        }

        $query = (new Query())
            ->select([
                'lessonId' => 'els.lesson_id',
                'topicId' => 't.id',
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
        $topicId = $rows[0]['topicId'];
        $backRoute = ['/edu/student/lesson', 'id' => $lessonId];

        $studentClassBookId = $this->studentClassFetcher->fetch($studentId);
        if ($studentClassBookId !== null) {
            $haveTopicAccess = (new Query())
                ->from('edu_class_book_topic_access')
                ->where([
                    'class_book_id' => $studentClassBookId,
                    'class_program_id' => $program->id,
                    'topic_id' => $topicId,
                ])
                ->exists();
            if (!$haveTopicAccess) {
                throw new ForbiddenHttpException('Доступ к теме запрещен');
            }
        }

        if (!Yii::$app->user->can(UserRoles::ROLE_TEACHER)) {
            $this->accessManager->checkAccessLesson($program->id, (int) $lessonId, $studentId, (int) $topicId);
        }

        $slideIds = array_map(static function(EduStorySlide $slide): int {
            return $slide->id;
        }, $story->storySlides);

        $contentMentalMaps = (new ContentMentalMapsFetcher())->fetch(
            $slideIds,
            $user->getId()
        );

        return $this->render('view', [
            'story' => $story,
            'programId' => $program_id,
            'backRoute' => $backRoute,
            'studentId' => $studentId,
            'contentMentalMaps' => $contentMentalMaps,
        ]);
    }
}
