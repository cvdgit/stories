<?php

declare(strict_types=1);

namespace modules\edu\controllers\teacher;

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
use modules\edu\RequiredStory\repo\RequiredStoriesRepository;
use modules\edu\RequiredStory\repo\RequiredStoryItem;
use modules\edu\RequiredStory\repo\RequiredStoryMetadata;
use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use modules\edu\RequiredStory\RequiredStoriesService;
use modules\edu\StoryContent\StoryContentService;
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

    public function __construct(
        $id,
        $module,
        RequiredStoriesRepository $requiredStoriesRepository,
        StoryContentService $storyContentService,
        EditRequiredStoryHandler $editRequiredStoryHandler,
        CreateRequiredStoryHandler $createRequiredStoryHandler,
        DeleteRequiredStoryHandler $deleteRequiredStoryHandler,
        RequiredStoriesService $requiredStoriesService,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->requiredStoriesRepository = $requiredStoriesRepository;
        $this->storyContentService = $storyContentService;
        $this->editRequiredStoryHandler = $editRequiredStoryHandler;
        $this->createRequiredStoryHandler = $createRequiredStoryHandler;
        $this->deleteRequiredStoryHandler = $deleteRequiredStoryHandler;
        $this->requiredStoriesService = $requiredStoriesService;
    }

    /**
     * @throws Exception
     */
    public function actionIndex(): string
    {
        $models = $this->requiredStoriesRepository->findAll();
        $models = array_map(function(RequiredStoryItem $model) {
            $session = $this->requiredStoriesService->findStudentSession(
                $model->getId(),
                new DateTimeImmutable('now', new DateTimeZone('Europe/Moscow'))
            );
            $stat = [
                'sessionFact' => 0,
                'sessionPlan' => 0,
                'sessionIsComplete' => false,
                'fact' => $this->storyContentService->getStudentFactContentItemsCount(
                    $model->getStudentId(),
                    $model->getStoryId()
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

        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => false,
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
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
        ]);

        $students = [];
        return $this->renderAjax('_edit_form', [
            'formModel' => $editForm,
            'students' => $students,
            'storyModel' => EduStory::findOne($requiredStory->getStoryId()),
            'studentModel' => EduStudent::findOne($requiredStory->getStudentId())->user,
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
    public function actionCreate(): string
    {
        $createForm = new RequiredStoryCreateForm([
            'days' => 1,
            'startDate' => Yii::$app->formatter->asDate('now', 'php:Y-m-d'),
            'status' => (string) RequiredStoryStatus::open(),
        ]);
        return $this->renderAjax('_create_form', [
            'formModel' => $createForm,
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
            ->andWhere(['exists', (new Query())->from(['t' => 'edu_class_book_student'])->where('t.student_id = us.id')])
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
    public function actionGetStoryContentsTotal(int $storyId, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        return [
            'total' => $this->storyContentService->getStoryTotalContentItems($storyId),
        ];
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
                    $user->getId()
                )
            );
        } catch (Exception $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['success' => false, 'message' => $exception->getMessage()];
        }
        return ['success' => true];
    }
}
