<?php

declare(strict_types=1);

namespace modules\edu\controllers;

use common\models\UserStudent;
use DateTimeImmutable;
use Exception;
use modules\edu\components\TopicAccessManager;
use modules\edu\models\EduClass;
use modules\edu\models\EduClassBook;
use modules\edu\models\EduClassProgram;
use modules\edu\models\EduLesson;
use modules\edu\models\EduTopic;
use modules\edu\query\StudentClassFetcher;
use modules\edu\RepetitionApiInterface;
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use modules\edu\RequiredStory\RequiredStoriesService;
use modules\edu\StoryContent\StoryContentService;
use modules\edu\widgets\StudentToolbarWidget;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StudentController extends Controller
{
    /** @var TopicAccessManager */
    private $topicAccessManager;
    /** @var RepetitionApiInterface */
    private $repetitionApi;

    private $studentClassFetcher;
    /**
     * @var RequiredStoriesRepository
     */
    private $requiredStoriesRepository;
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
        TopicAccessManager $topicAccessManager,
        RepetitionApiInterface $repetitionApi,
        StudentClassFetcher $studentClassFetcher,
        RequiredStoriesRepository $requiredStoriesRepository,
        RequiredStoriesService $requiredStoriesService,
        StoryContentService $storyContentService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->topicAccessManager = $topicAccessManager;
        $this->repetitionApi = $repetitionApi;
        $this->studentClassFetcher = $studentClassFetcher;
        $this->requiredStoriesRepository = $requiredStoriesRepository;
        $this->requiredStoriesService = $requiredStoriesService;
        $this->storyContentService = $storyContentService;
    }

    /*public function behaviors(): array
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
     * @throws BadRequestHttpException
     */
    private function getStudentClass(int $classBookId = null, EduClass $class = null): EduClass
    {
        if ($classBookId !== null) {
            $classBook = EduClassBook::findOne($classBookId);
            if ($classBook === null) {
                throw new BadRequestHttpException('Класс не найден');
            }
            $studentClass = $classBook->class;
        } else {
            $studentClass = $class;
        }

        if ($studentClass === null) {
            throw new BadRequestHttpException('Не удалось определить класс');
        }

        return $studentClass;
    }

    /**
     * @throws ForbiddenHttpException
     */
    private function getStudent(): UserStudent
    {
        $student = Yii::$app->studentContext->getStudent();
        if ($student === null) {
            $student = Yii::$app->user->identity->student();
            if ($student === null) {
                throw new ForbiddenHttpException('Доступ запрещен');
            }
        }
        return $student;
    }

    /**
     * @throws ForbiddenHttpException|BadRequestHttpException
     */
    public function actionIndex(): string
    {
        $student = $this->getStudent();
        $studentClassBookId = $this->studentClassFetcher->fetch($student->id);
        $studentClass = $this->getStudentClass($studentClassBookId, $student->class);

        /*$classProgramIds = array_map(static function($classProgram) {
            return $classProgram->id;
        }, $studentClass->eduClassPrograms);*/

        // Классы, в которых состоит ученик
        $classBooks = $student->classBooks;
        $classProgramIds = [];

        if (count($classBooks) === 0) {
            // Если ученик не состоит ни в одном классе
            $classProgramIds = array_map(static function($classProgram) {
                return $classProgram->id;
            }, $studentClass->eduClassPrograms);
        }
        else {
            foreach ($classBooks as $classBook) {
                $classProgramIds = array_merge($classProgramIds, $classBook->getClassProgramIds());
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => EduClassProgram::find()->where(['in', 'id', $classProgramIds]),
        ]);

        $repetitionDataProvider = $this->repetitionApi->getRepetitionDataProvider($student->id);

        return $this->render('index', [
            'student' => $student,
            'studentToolbarWidget' => $this->renderStudentToolbarWidget($student, $studentClass),
            'dataProvider' => $dataProvider,
            'repetitionDataProvider' => $repetitionDataProvider,
            'classBookId' => $studentClassBookId,
            'requiredStoriesWidgetConfig' => ['studentId' => $student->id],
        ]);
    }

    /**
     * @throws NotFoundHttpException|ForbiddenHttpException|BadRequestHttpException
     */
    public function actionTopic(int $id): string
    {
        if (($topic = EduTopic::findOne($id)) === null) {
            throw new NotFoundHttpException('Тема не найдена');
        }

        $student = $this->getStudent();

        /*$studentClassId = $this->studentClassFetcher->fetch($student->id);
        if ($studentClassId !== null) {
            $studentClass = EduClass::findOne($studentClassId);
        } else {
            $studentClass = $student->class;
            if ($studentClass === null) {
                throw new BadRequestHttpException('Не удалось определить класс');
            }
        }*/

        $studentClassBookId = $this->studentClassFetcher->fetch($student->id);
        $studentClass = $this->getStudentClass($studentClassBookId, $student->class);

        $classProgram = $topic->classProgram;

        if ($studentClassBookId !== null) {
            $haveTopicAccess = (new Query())
                ->from('edu_class_book_topic_access')
                ->where([
                    'class_book_id' => $studentClassBookId,
                    'class_program_id' => $topic->class_program_id,
                    'topic_id' => $topic->id,
                ])
                ->exists();
            if (!$haveTopicAccess) {
                throw new ForbiddenHttpException('Доступ к теме запрещен');
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $topic->getEduLessons(),
        ]);

        $topics = $studentClassBookId === null ? $classProgram->eduTopics :  $classProgram->getEduTopicsWithAccess($studentClassBookId)->all();
        $topicIds = array_map(static function($topic) {
            return $topic->id;
        }, $topics);

        $lessonAccess = $this->topicAccessManager->getStudentLessonAccess($classProgram->id, $student->id, $topicIds);

        return $this->render('topic', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'studentToolbarWidget' => $this->renderStudentToolbarWidget($student, $studentClass),
            'topics' => $topics,
            'dataProvider' => $dataProvider,
            'topic' => $topic,
            'lessonAccess' => $lessonAccess,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function actionLesson(int $id): string
    {
        if (($lesson = EduLesson::findOne($id)) === null) {
            throw new NotFoundHttpException('Урок не найден');
        }

        $student = $this->getStudent();
        $studentClassBookId = $this->studentClassFetcher->fetch($student->id);
        $studentClass = $this->getStudentClass($studentClassBookId, $student->class);

        $topic = $lesson->topic;

        if ($studentClassBookId !== null) {
            $haveTopicAccess = (new Query())
                ->from('edu_class_book_topic_access')
                ->where([
                    'class_book_id' => $studentClassBookId,
                    'class_program_id' => $topic->class_program_id,
                    'topic_id' => $topic->id,
                ])
                ->exists();
            if (!$haveTopicAccess) {
                throw new ForbiddenHttpException('Доступ к теме запрещен');
            }
        }

        $this->topicAccessManager->getStudentLessonAccess($topic->class_program_id, $student->id, [$topic->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $lesson->getStories(),
        ]);


        $classProgram = $topic->classProgram;
        $topics = $studentClassBookId === null ? $classProgram->eduTopics :  $classProgram->getEduTopicsWithAccess($studentClassBookId)->all();

        return $this->render('lesson', [
            'classProgramName' => $classProgram->program->name,
            'student' => $student,
            'topics' => $topics,
            'dataProvider' => $dataProvider,
            'lesson' => $lesson,
            'currentTopicId' => $topic->id,
            'programId' => $classProgram->id,
            'studentToolbarWidget' => $this->renderStudentToolbarWidget($student, $studentClass),
        ]);
    }

    private function renderStudentToolbarWidget(UserStudent $student, EduClass $class): string
    {
        return StudentToolbarWidget::widget(['studentName' => $student->name, 'studentClassName' => $class->name]);
    }

    /**
     * @throws Exception
     */
    public function actionRequiredStoryStat(int $studentId, int $storyId, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $requiredStory = $this->requiredStoriesRepository->findRequiredStory($storyId, $studentId);
        if ($requiredStory === null) {
            return ['success' => false];
        }

        $requiredStorySession = $this->requiredStoriesService->initSession(
            $studentId,
            $storyId,
            $requiredStory->getId(),
            $requiredStory->getMetadata(),
            new DateTimeImmutable()
        );
        $requiredStoryStat = [
            'sessionFact' => $requiredStorySession->getFact(),
            'sessionPlan' => $requiredStorySession->getPlan(),
            'sessionIsCompleted' => $requiredStorySession->isCompleted(),
            'plan' => $this->storyContentService->getStoryTotalContentItems($storyId),
            'fact' => $this->storyContentService->getStudentFactContentItemsCount(
                $studentId,
                $storyId,
            ),
        ];
        return [
            'success' => true,
            'content' => $this->renderPartial('../story/_required_story_stat', $requiredStoryStat),
        ];
    }
}
