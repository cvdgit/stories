<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use backend\SlideEditor\SlideSettings\SlideSettingsPayload;
use common\rbac\UserRoles;
use common\widgets\Reveal\Plugins\SpeakSlideText;
use DateTimeImmutable;
use frontend\MentalMap\Content\ContentMentalMapsFetcher;
use modules\edu\components\TopicAccessManager;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduStory;
use modules\edu\models\EduStorySlide;
use modules\edu\query\ClassProgramsByStoryFetcher;
use modules\edu\query\StudentClassFetcher;
use modules\edu\RequiredStory\RequiredStoriesService;
use modules\edu\StoryContent\StoryContentService;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Expression;
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
    /**
     * @var RequiredStoriesService
     */
    private $requiredStoriesService;
    /**
     * @var StoryContentService
     */
    private $storyContentService;

    public function __construct(
        $id,
        $module,
        TopicAccessManager $accessManager,
        StudentClassFetcher $studentClassFetcher,
        RequiredStoriesService $requiredStoriesService,
        StoryContentService $storyContentService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->accessManager = $accessManager;
        $this->studentClassFetcher = $studentClassFetcher;
        $this->requiredStoriesService = $requiredStoriesService;
        $this->storyContentService = $storyContentService;
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
     * @throws Exception|InvalidConfigException
     */
    public function actionView(int $id, WebUser $user, int $program_id = null)
    {
        if (($story = EduStory::findOne($id)) === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        if ($program_id === null) {
            $classProgramIds = (new ClassProgramsByStoryFetcher())->fetch($story->id);
            if (count($classProgramIds) === 0) {
                throw new BadRequestHttpException('Не удалось определить программу обучения');
            }
            return $this->redirect(['/edu/story/view', 'id' => $story->id, 'program_id' => $classProgramIds[0]]);
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

        $requiredStory = $this->requiredStoriesService->storyIsRequired($story->id, $studentId);
        if (($requiredStory === null) && !$this->requiredStoriesService->isAccessToOtherStories($studentId)) {
            throw new ForbiddenHttpException('Запрещено т.к. не пройдены обязательные истории на сегодня');
        }

        $requiredStorySession = null;
        $requiredStoryStat = ['plan' => 0, 'fact' => 0];
        $requiredStoryIsDone = $this->requiredStoriesService->storyIsDone($studentId, $story->id);

        if ($requiredStory !== null && $requiredStoryIsDone === false) {
            $requiredStorySession = $this->requiredStoriesService->initSession(
                $studentId,
                $story->id,
                $requiredStory->getId(),
                $requiredStory->getMetadata(),
                new DateTimeImmutable()
            );
            $requiredStoryStat['plan'] = $this->storyContentService->getStoryTotalContentItems($story->id);
            $requiredStoryStat['fact'] = $this->storyContentService->getStudentFactContentItemsCount(
                $studentId,
                $story->id
            );
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

        if ($requiredStory === null) {
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
        }

        $slideIds = array_map(static function (EduStorySlide $slide): int {
            return $slide->id;
        }, $story->storySlides);

        $contentMentalMaps = (new ContentMentalMapsFetcher())->fetch(
            $slideIds,
            $user->getId(),
        );

        $ids = (new Query())
            ->select([
                'slideId' => 'rh.slide_id',
                'overallSimilarity' => new Expression('MAX(rh.overall_similarity)'),
            ])
            ->from(['rh' => 'retelling_history'])
            ->where([
                'story_id' => $id,
                'user_id' => Yii::$app->user->getId(),
            ])
            ->andWhere('rh.overall_similarity >= ' . SpeakSlideText::SPEAK_SLIDE_TEXT_THRESHOLD)
            ->groupBy(['rh.slide_id'])
            ->all();
        $completedRetelling = array_map(static function (array $item): int {
            return (int) $item['slideId'];
        }, $ids);

        $speakTextSlides = [];
        foreach ($story->storySlides as $slide) {
            if ($slide->settings === null) {
                continue;
            }
            $settings = SlideSettingsPayload::fromPayload($slide->settings);
            if ($settings->isSpeakSlideText()) {
                $speakTextSlides[] = [
                    'slideId' => $slide->id,
                    'passed' => in_array($slide->id, $completedRetelling, true),
                ];
            }
        }

        return $this->render('view', [
            'story' => $story,
            'programId' => $program_id,
            'backRoute' => $backRoute,
            'studentId' => $studentId,
            'contentMentalMaps' => $contentMentalMaps,
            'requiredStory' => $requiredStory,
            'requiredStorySession' => $requiredStorySession,
            'requiredStoryStat' => $requiredStoryStat,
            'requiredStoryIsDone' => $requiredStoryIsDone,
            'speakTextSlides' => $speakTextSlides,
        ]);
    }
}
