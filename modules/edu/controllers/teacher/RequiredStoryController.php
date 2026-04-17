<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

use common\models\StoryStudentProgress;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use modules\edu\models\EduStory;
use modules\edu\models\EduStudent;
use modules\edu\models\EduUser;
use modules\edu\RequiredStory\Create\CreateRequiredStoryCommand;
use modules\edu\RequiredStory\Create\CreateRequiredStoryHandler;
use modules\edu\RequiredStory\Create\RequiredStoryCreateForm;
use modules\edu\RequiredStory\Delete\DeleteRequiredStoryCommand;
use modules\edu\RequiredStory\Delete\DeleteRequiredStoryHandler;
use modules\edu\RequiredStory\Edit\EditRequiredStoryCommand;
use modules\edu\RequiredStory\Edit\EditRequiredStoryHandler;
use modules\edu\RequiredStory\Edit\RequiredStoryEditForm;
use modules\edu\RequiredStory\repo\ByStoriesItem;
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use modules\edu\RequiredStory\repo\RequiredStory;
use modules\edu\RequiredStory\repo\RequiredStoryItem;
use modules\edu\RequiredStory\repo\RequiredStoryMetadata;
use modules\edu\RequiredStory\repo\RequiredStorySessionRepository;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use modules\edu\RequiredStory\RequiredStoriesService;
use modules\edu\StoryContent\StoryContentService;
use modules\edu\StoryProgress\StoryProgressFetcher;
use modules\edu\Teacher\ClassBook\TeacherAccess\UserItem;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class RequiredStoryController extends Controller
{
    /**
     * @var RequiredStoriesRepository
     */
    private $requiredStoriesRepository;
    /**
     * @var StoryContentService
     */
    private $storyContentService;
    /**
     * @var EditRequiredStoryHandler
     */
    private $editRequiredStoryHandler;
    /**
     * @var CreateRequiredStoryHandler
     */
    private $createRequiredStoryHandler;
    /**
     * @var DeleteRequiredStoryHandler
     */
    private $deleteRequiredStoryHandler;
    /**
     * @var RequiredStoriesService
     */
    private $requiredStoriesService;
    /**
     * @var RequiredStorySessionRepository
     */
    private $requiredStorySessionRepository;
    /**
     * @var StoryProgressFetcher
     */
    private $storyProgressFetcher;

    public function __construct(
        $id,
        $module,
        RequiredStoriesRepository $requiredStoriesRepository,
        RequiredStorySessionRepository $requiredStorySessionRepository,
        StoryContentService $storyContentService,
        EditRequiredStoryHandler $editRequiredStoryHandler,
        CreateRequiredStoryHandler $createRequiredStoryHandler,
        DeleteRequiredStoryHandler $deleteRequiredStoryHandler,
        RequiredStoriesService $requiredStoriesService,
        StoryProgressFetcher $storyProgressFetcher,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->requiredStoriesRepository = $requiredStoriesRepository;
        $this->storyContentService = $storyContentService;
        $this->editRequiredStoryHandler = $editRequiredStoryHandler;
        $this->createRequiredStoryHandler = $createRequiredStoryHandler;
        $this->deleteRequiredStoryHandler = $deleteRequiredStoryHandler;
        $this->requiredStoriesService = $requiredStoriesService;
        $this->requiredStorySessionRepository = $requiredStorySessionRepository;
        $this->storyProgressFetcher = $storyProgressFetcher;
    }

    /**
     * @throws Exception
     */
    public function actionIndex(int $studentId = null): string
    {
        $studentIds = $this->requiredStoriesRepository->findStudentIds();
        $students = EduStudent::find()->where(['in', 'id', $studentIds])->all();

        if ($studentId === null) {
            $models = $this->requiredStoriesRepository->findAllByStories();
            $models = array_map(function (ByStoriesItem $model) {
                $stat = $this->storyProgressFetcher->fetchStudentsStoryStatus(
                    $model->getStoryId(),
                    $model->getStudentIds(),
                );
                return [$model, $stat];
            }, $models);

            $dataProvider = new ArrayDataProvider([
                'allModels' => $models,
                'pagination' => false,
            ]);
            return $this->render('index_stories', [
                'students' => $students,
                'dataProvider' => $dataProvider,
                'activeStudentId' => null,
            ]);
        }

        $models = $this->requiredStoriesRepository->findAll($studentId);

        $storyIds = array_map(static function (RequiredStoryItem $requiredStory) {
            return $requiredStory->getStoryId();
        }, $models);

        $progressModels = StoryStudentProgress::find()
            ->where(['student_id' => $studentId])
            ->andWhere(['in', 'story_id', $storyIds])
            ->all();
        $storyDoneProgress = array_combine(
            array_map(static function (StoryStudentProgress $progress) {
                return $progress->story_id;
            }, $progressModels),
            array_map(static function (StoryStudentProgress $progress) {
                return $progress->statusIsDone();
            }, $progressModels),
        );

        $models = array_map(function (RequiredStoryItem $model) use ($storyDoneProgress) {
            $storyIsDone = $storyDoneProgress[$model->getStoryId()] ?? false;
            if ($storyIsDone) {
                return [
                    $model,
                    ['done' => true],
                ];
            }

            $session = $this->requiredStoriesService->findStudentSession(
                $model->getId(),
                new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')),
            );
            $stat = [
                'done' => false,
                'sessionFact' => 0,
                'sessionPlan' => 0,
                'sessionIsComplete' => false,
                'fact' => $this->storyContentService->getStudentFactContentItemsCount(
                    $model->getStudentId(),
                    $model->getStoryId(),
                ),
                'plan' => $this->storyContentService->getStoryTotalContentItems($model->getStoryId()),
            ];
            if ($session !== null) {
                $stat['sessionFact'] = $session->getFact();
                $stat['sessionPlan'] = $session->getPlan();
                $stat['sessionIsCompleted'] = $session->isCompleted();
            }
            return [$model, $stat];
        }, $models);

        $todayPlan = $this->requiredStoriesService->getStudentPlan($studentId);
        $todayFact = $this->requiredStoriesService->getStudentFact(
            $studentId,
            new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow')),
        );

        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => false,
        ]);
        return $this->render('index', [
            'students' => $students,
            'dataProvider' => $dataProvider,
            'activeStudentId' => $studentId,
            'todayPlan' => $todayPlan,
            'todayFact' => $todayFact,
        ]);
    }

    /**
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionEdit(string $id): string
    {
        if (!Uuid::isValid($id)) {
            throw new BadRequestHttpException('Id error');
        }

        $requiredStory = $this->requiredStoriesRepository->findById(Uuid::fromString($id));
        if ($requiredStory === null) {
            throw new NotFoundHttpException('Required story not found');
        }

        $editForm = new RequiredStoryEditForm([
            'id' => $requiredStory->getId()->toString(),
            'storyId' => $requiredStory->getStoryId(),
            'studentId' => $requiredStory->getStudentId(),
            'startDate' => $requiredStory->getStartedAt()->format('Y-m-d'),
            'days' => $requiredStory->getDays(),
            'metadata' => Json::encode($requiredStory->getMetadata()),
            'status' => (string) $requiredStory->getStatus(),
            'storyStudentFact' => $this->storyContentService->getStudentFactContentItemsCount(
                $requiredStory->getStudentId(),
                $requiredStory->getStoryId(),
            ),
        ]);

        $students = [];

        $selectedStudent = EduStudent::findOne($requiredStory->getStudentId());
        if ($selectedStudent === null) {
            throw new NotFoundHttpException('Student not found');
        }

        return $this->renderAjax('_edit_form', [
            'formModel' => $editForm,
            'students' => $students,
            'storyModel' => EduStory::findOne($requiredStory->getStoryId()),
            'studentModel' => new UserItem(
                $selectedStudent->id,
                $selectedStudent->name,
                $selectedStudent->user->email,
                $selectedStudent->user->getProfilePhoto(),
            ),
        ]);
    }

    public function actionEditHandler(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $editForm = new RequiredStoryEditForm();
        if ($editForm->load($request->post())) {
            if (!$editForm->validate()) {
                return ['success' => false, 'message' => implode(PHP_EOL, $editForm->getErrorSummary(true))];
            }
            try {
                $metadata = RequiredStoryMetadata::fromArray(
                    Json::decode($editForm->metadata),
                );
                $this->editRequiredStoryHandler->handle(
                    new EditRequiredStoryCommand(
                        Uuid::fromString($editForm->id),
                        (int) $editForm->storyId,
                        (int) $editForm->studentId,
                        new DateTimeImmutable($editForm->startDate, new DateTimeZone('Europe/Moscow')),
                        (int) $editForm->days,
                        new RequiredStoryStatus($editForm->status),
                        $metadata,
                    ),
                );
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionCreate(WebUser $user): string
    {
        $createForm = new RequiredStoryCreateForm([
            'days' => 1,
            'startDate' => Yii::$app->formatter->asDate('now', 'php:Y-m-d'),
            'status' => (string) RequiredStoryStatus::open(),
        ]);

        $studentIds = $this->requiredStoriesRepository->findStudentIds($user->getId());
        $students = EduStudent::find()->where(['in', 'id', $studentIds])->all();

        return $this->renderAjax('_create_form', [
            'formModel' => $createForm,
            'students' => array_map(static function (EduStudent $student) {
                return new UserItem(
                    $student->id,
                    $student->name,
                    $student->user->email,
                    $student->user->getProfilePhoto(),
                );
            }, $students),
        ]);
    }

    public function actionCreateHandler(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $createForm = new RequiredStoryCreateForm();
        if ($createForm->load($request->post())) {
            if (!$createForm->validate()) {
                return ['success' => false, 'message' => implode(PHP_EOL, $createForm->getErrorSummary(true))];
            }
            try {
                $metadata = RequiredStoryMetadata::fromArray(
                    Json::decode($createForm->metadata),
                );
                $this->createRequiredStoryHandler->handle(
                    new CreateRequiredStoryCommand(
                        Uuid::uuid4(),
                        (int) $createForm->storyId,
                        (int) $createForm->studentId,
                        $user->getId(),
                        new DateTimeImmutable($createForm->startDate, new DateTimeZone('Europe/Moscow')),
                        (int) $createForm->days,
                        new RequiredStoryStatus($createForm->status),
                        $metadata,
                    ),
                );
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }

    public function actionSelectStudents(string $query, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        return (new Query())
            ->select([
                'title' => new Expression("CONCAT(us.name, ' (', cb.name, ')')"),
                'email' => 'u.email',
                'id' => 'us.id',
                'cover' => new Expression("'/img/no_avatar.png'"),
            ])
            ->from(['sl' => 'student_login'])
            ->innerJoin(['us' => EduStudent::tableName()], 'sl.student_id = us.id')
            ->innerJoin(['u' => EduUser::tableName()], 'us.user_id = u.id')
            ->innerJoin(['t' => 'edu_class_book_student'], 't.student_id = us.id')
            ->innerJoin(['cb' => 'edu_class_book'], 't.class_book_id = cb.id')
            ->where([
                'or',
                ['like', 'us.name', $query],
                ['like', 'u.username', $query],
                ['like', 'u.email', $query],
            ])
            ->andWhere(['u.status' => 10])
            ->andWhere(['exists', (new Query())->from(['t' => 'edu_class_book_student'])->where('t.student_id = us.id')],
            )
            ->orderBy(['us.name' => SORT_ASC])
            ->limit(30)
            ->all();
    }

    public function actionSelectStories(string $query, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        return (new Query())
            ->select(
                ['title', 'id', "IF(cover IS NULL, '/img/story-1.jpg', CONCAT('/slides_cover/list/', cover)) AS cover"],
            )
            ->from(EduStory::tableName())
            ->where(['like', 'title', $query])
            ->orderBy(['title' => SORT_ASC])
            ->limit(30)
            ->all();
    }

    /**
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionGetStoryContentsTotal(int $storyId, Response $response, int $studentId = null): array
    {
        $response->format = Response::FORMAT_JSON;
        $result = [
            'total' => $this->storyContentService->getStoryTotalContentItems($storyId),
            'fact' => 0,
        ];
        if ($studentId !== null) {
            $result['fact'] = $this->storyContentService->getStudentFactContentItemsCount(
                $studentId,
                $storyId,
            );
        }
        return $result;
    }

    public function actionDelete(string $id, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        try {
            if (!Uuid::isValid($id)) {
                throw new BadRequestHttpException('Id not valid');
            }
            $this->deleteRequiredStoryHandler->handle(
                new DeleteRequiredStoryCommand(
                    Uuid::fromString($id),
                    $user->getId(),
                ),
            );
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
        return ['success' => true];
    }

    /**
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionSessions(string $id): string
    {
        if (!Uuid::isValid($id)) {
            throw new BadRequestHttpException('Id error');
        }
        $requiredStory = $this->requiredStoriesRepository->findById(Uuid::fromString($id));
        if ($requiredStory === null) {
            throw new NotFoundHttpException('Required story not found');
        }
        $story = EduStory::findOne($requiredStory->getStoryId());
        if ($story === null) {
            throw new NotFoundHttpException('Story not found');
        }
        $student = EduStudent::findOne($requiredStory->getStudentId());
        if ($student === null) {
            throw new NotFoundHttpException('Student not found');
        }
        return $this->renderAjax('_sessions', [
            'story' => $story,
            'student' => $student,
            'sessions' => $this->requiredStorySessionRepository->findByRequiredStoryId(
                Uuid::fromString($id),
            ),
        ]);
    }
}
