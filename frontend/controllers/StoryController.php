<?php

namespace frontend\controllers;

use backend\components\book\BookStoryGenerator;
use backend\components\training\base\Serializer;
use backend\components\training\collection\TestBuilder;
use common\components\MentalMapThreshold;
use common\helpers\UserHelper;
use common\models\Playlist;
use common\models\SiteSection;
use common\models\StorySlide;
use common\models\StoryTest;
use common\models\User;
use common\models\UserQuestionHistoryModel;
use common\rbac\UserRoles;
use common\services\story\CountersService;
use common\services\StoryAudioService;
use common\services\StoryFavoritesService;
use common\services\StoryLikeService;
use common\services\QuestionsService;
use frontend\components\StoryRenderParams;
use frontend\GptChat\GptChatForm;
use frontend\MentalMap\history\MentalMapTreeHistoryFetcher;
use frontend\MentalMap\MentalMap;
use frontend\MentalMap\MentalMapStorySlide;
use frontend\models\CreateStoryTestRun;
use frontend\models\MyAudioStoriesSearch;
use frontend\models\StoryFavoritesSearch;
use frontend\models\StoryLikeForm;
use frontend\models\StoryLikeSearch;
use frontend\models\StoryTrackModel;
use frontend\models\UserStorySearch;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\Story;
use frontend\models\StorySearch;
use common\models\Tag;
use common\models\Category;
use common\models\Comment;
use common\services\StoryService;
use frontend\models\CommentForm;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\debug\Module;
use yii\web\View;
use yii\web\User as WebUser;

class StoryController extends Controller
{

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['add-comment', 'comment-list', 'history', 'liked', 'favorites', 'myaudio'],
                'rules' => [
                    [
                        'actions' => ['add-comment', 'comment-list', 'history', 'liked', 'favorites', 'myaudio'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    protected $storyService;
    protected $countersService;
    protected $likeService;
    protected $favoritesService;
    protected $questionsService;
    protected $audioService;
    private $bookStoryGenerator;

    public function __construct(
        $id,
        $module,
        StoryService $storyService,
        CountersService $countersService,
        StoryLikeService $likeService,
        StoryFavoritesService $favoritesService,
        QuestionsService $questionsService,
        StoryAudioService $audioService,
        BookStoryGenerator $bookStoryGenerator,
        $config = []
    ) {
        $this->storyService = $storyService;
        $this->countersService = $countersService;
        $this->likeService = $likeService;
        $this->favoritesService = $favoritesService;
        $this->questionsService = $questionsService;
        $this->audioService = $audioService;
        $this->bookStoryGenerator = $bookStoryGenerator;
        parent::__construct($id, $module, $config);
    }

    private function findSectionModel(string $alias): ?SiteSection
    {
        if (($model = SiteSection::findOne(['alias' => $alias])) !== null && $model->isVisible()) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    private function findCategoryModel(string $alias): ?Category
    {
        if (($model = Category::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена');
    }

    public function actionIndex(string $section)
    {
        $sectionModel = $this->findSectionModel($section);
        $this->getView()->setMetaTags(
            $sectionModel->title,
            $sectionModel->description,
            $sectionModel->keywords,
            $sectionModel->h1,
        );

        $searchModel = new StorySearch();
        $searchModel->category_id = $sectionModel->getSectionCategories();
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSectionModel($sectionModel)
                ->setSearchModel($searchModel, Yii::$app->request->queryParams)
                ->asArray(),
        );
    }

    public function actionCategory(string $section, string $category)
    {
        $sectionModel = $this->findSectionModel($section);
        $model = $this->findCategoryModel($category);

        if (!$sectionModel->isOurCategory($model)) {
            throw new NotFoundHttpException('Категория не найдена');
        }

        $this->getView()->setMetaTags(
            $model->name . ' - каталог историй',
            $model->name,
            'wikids, сказки, истории, каталог историй',
            $model->name,
        );

        $searchModel = new StorySearch();
        if (!empty($model->sort_field)) {
            $searchModel->defaultSortField = $model->sort_field;
            $searchModel->defaultSortOrder = !empty($model->sort_order) ? $model->sort_order : SORT_ASC;
        }
        $searchModel->category_id = $model->subCategories();
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSectionModel($sectionModel)
                ->setCategoryModel($model)
                ->setSearchModel($searchModel, Yii::$app->request->queryParams)
                ->asArray(),
        );
    }

    public function actionTag($tag)
    {
        $model = Tag::findModelByName($tag);

        $this->getView()->setMetaTags(
            $model->name . ' - каталог историй',
            $model->name,
            'wikids, сказки, истории, каталог историй',
            $model->name,
        );

        $searchModel = new StorySearch();
        $searchModel->tag_id = $model->id;
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSearchModel($searchModel, Yii::$app->request->queryParams)
                ->setSearchAction(['/story/tag', 'tag' => $model->name])
                ->asArray(),
        );
    }

    /**
     * @throws NotFoundHttpException
     * @return Response|string
     */
    public function actionView(string $alias, Request $request, $track_id = null)
    {
        $model = Story::findOne(['alias' => $alias]);
        if ($model === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        if (Yii::$app->user->isGuest && !$model->isPublished()) {
            throw new NotFoundHttpException('История не найдена');
        }

        $getCourseUserId = $request->get('get_course_id');
        if ($getCourseUserId !== null && Yii::$app->user->isGuest) {
            $user = User::findOne(['get_course_id' => (int)$getCourseUserId]);
            if ($user !== null) {
                Yii::$app->user->login($user, Yii::$app->params['user.rememberMeDuration']);
                return $this->refresh();
            }
        }

        $playlistID = $request->get('list');
        $playlist = null;
        if ($playlistID !== null) {
            $playlist = Playlist::findModel((int) $playlistID);
        }

        $dataProvider = Comment::getCommentDataProvider($model->id);
        if ($request->isPjax) {
            return $this->renderAjax('_comment_list', ['dataProvider' => $dataProvider]);
        }

        $this->countersService->updateCounters($model);

        $commentForm = new CommentForm($model->id);

        $storyDefaultView = $this->storyService->getDefaultStoryView();
        $guestStoryBody = '';
        if ($storyDefaultView === 'book') {
            $guestStoryBody = $this->bookStoryGenerator->generate($model);
        }

        return $this->render('view', [
            'model' => $model,
            'storyDefaultView' => $storyDefaultView,
            'commentForm' => $commentForm,
            'dataProvider' => $dataProvider,
            'trackID' => $track_id,
            'playlist' => $playlist,
            'guestStoryBody' => $guestStoryBody,
        ]);
    }

    public function actionAddComment($id)
    {
        if (Yii::$app->request->isPjax) {
            $commentForm = new CommentForm($id);
            if ($commentForm->load(Yii::$app->request->post()) && $commentForm->validate()) {
                $commentForm->createComment(Yii::$app->user->id);
                $commentForm->body = '';
            }
            return $this->renderAjax('_comment_form', ['commentForm' => $commentForm]);
        } else {
            return $this->goHome();
        }
    }

    protected function slideFilterArray($filter)
    {
        $slides = [];
        foreach (explode(',', $filter) as $value) {
            if (strpos($value, '-') !== false) {
                [$a, $b] = explode('-', $value);
                for ($i = $a; $i <= $b; $i++) {
                    $slides[] = $i;
                }
            } else {
                $slides[] = $value;
            }
        }
        return $slides;
    }

    public function actionGetStoryBody($id, $filter = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Story::findModel($id);
        $html = $model->slidesData();
        if (!empty($filter)) {
            $slides = [];
            $slideFilter = $this->slideFilterArray($filter);
            foreach ($slideFilter as $slideIndex) {
                $slide = StorySlide::findSlideByNumber($model->id, $slideIndex);
                /**  && $slide->status === StorySlide::STATUS_VISIBLE */
                if ($slide !== null) {
                    $slides[] = $slide->data;
                }
            }
            $html = implode("\n", $slides);
        }
        return ['html' => $html];
    }

    protected function findTestModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetStoryTest(int $id, int $studentId = null, bool $fastMode = false)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $test = $this->findTestModel($id);

        if (!Yii::$app->user->isGuest) {
            $testRunModel = new CreateStoryTestRun();
            $testRunModel->test_id = $id;
            $testRunModel->student_id = UserHelper::getCurrentUserStudentID();
            $testRunModel->createStoryTestRun();
        }

        $userHistory = [];
        $userStars = [];
        $userStarsCount = 0;
        if ($studentId !== null && !$fastMode) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $studentId;
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistoryLocal($test->id);
            $userStars = $userQuestionHistoryModel->getUserQuestionHistoryStarsLocal($test->id);
            $userStarsCount = $userQuestionHistoryModel->getUserHistoryStarsCountLocal($test->id);
        }
        $collection = (new TestBuilder(
            $test,
            $test->getQuestionData($userHistory),
            $test->getQuestionDataCount(),
            $userStars,
            $fastMode,
        ))
            ->build();
        return (new Serializer())
            ->serialize($test, $collection, $this->getStudents($test->id), $userStarsCount, $fastMode);
    }

    protected function getStudents(int $testID)
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
                    'progress' => (int) $student->getProgress($testID),
                ];
            }
        }
        return $students;
    }

    public function actionStoreTestResult(int $story_id, int $question_id, string $answers)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $correctAnswer = $this->questionsService->checkAnswer($question_id, $answers);
        if (!Yii::$app->user->isGuest) {
            $this->questionsService->storeQuestionResult($story_id, $question_id, $correctAnswer);
        }
        return ['success' => true, 'correctAnswer' => $correctAnswer];
    }

    public function actionInitStoryPlayer(int $id, WebUser $user)
    {
        if (Yii::$app->user->isGuest) {
            $model = Story::findModel(Yii::$app->params['story.needSignup.id']);
        } else {
            $model = Story::findModel($id);
            /*$userCanViewStory = UserPermissions::canViewStory($model);
            if (!$userCanViewStory) {
                $model = Story::findModel(Yii::$app->params['story.bySubscription.id']);
            }*/
        }

        $audioTrackPath = '';
        if ($model->isAudioStory() || $model->isUserAudioStory(Yii::$app->user->id)) {
            $track_id = Yii::$app->request->get('track_id');
            $track = $this->audioService->getStoryTrack($model, $track_id, Yii::$app->user->id);
            if ($track !== null) {
                $audioTrackPath = StoryTrackModel::getTrackRelativePath($model->id, $track->id);
            }
        }

        $completedRetelling = [];
        /*if (!Yii::$app->user->isGuest) {
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
                ->andWhere('rh.overall_similarity > 90')
                ->groupBy(['rh.slide_id'])
                ->all();
            $completedRetelling = array_map(static function (array $item): int {
                return (int) $item['slideId'];
            }, $ids);
        }*/

        $slideIds = array_map(static function(StorySlide $slide): int {
            return $slide->id;
        }, $model->storySlides);

        $contentMentalMaps = [];
        if (count($slideIds) > 0) {
            $slideMentalMaps = (new Query())
                ->select([
                    'slideId' => 't.slide_id',
                    'mentalMapId' => 't.mental_map_id',
                    'mentalMapName' => 't2.name',
                ])
                ->from(['t' => MentalMapStorySlide::tableName()])
                ->innerJoin(['t2' => MentalMap::tableName()], 't.mental_map_id = t2.uuid')
                ->where(['in', 't.slide_id', $slideIds])
                ->all();
            $canEdit = $user->can(UserRoles::ROLE_TEACHER);
            foreach ($slideMentalMaps as $row) {
                $slideId = (int) $row['slideId'];
                if (!isset($contentMentalMaps[$slideId])) {
                    $contentMentalMaps[$slideId] = [
                        'slideId' => $slideId,
                        'mentalMaps' => [],
                    ];
                }

                $mentalMap = MentalMap::findOne($row['mentalMapId']);
                if ($mentalMap === null) {
                    continue;
                }

                $threshold = MentalMapThreshold::getThreshold(Yii::$app->params, $mentalMap->payload);
                $history = (new MentalMapTreeHistoryFetcher())->fetch($mentalMap->uuid, $user->getId(), $mentalMap->getTreeData(), $threshold);

                $contentMentalMaps[$slideId]['mentalMaps'][] = [
                    'id' => $mentalMap->uuid,
                    'name' => $mentalMap->name,
                    'userProgress' => round(
                        count(array_filter($history, static function (array $item): bool {
                            return $item['done'];
                        })) * 100 / count($history),
                        0,
                        PHP_ROUND_HALF_UP,
                    ),
                    'type' => $mentalMap->map_type,
                    'edit' => $canEdit ? [
                        'url' => Yii::$app->urlManagerBackend->createAbsoluteUrl(['/mental-map/editor', 'id' => $mentalMap->uuid]),
                    ] : false,
                ];
            }
        }

        return $this->renderAjax('_player', [
            'model' => $model,
            'userCanViewStory' => true,
            'audioTrackPath' => $audioTrackPath,
            'playlistID' => Yii::$app->request->get('list'),
            'saveStat' => $this->countersService->needUpdateCounters(),
            'completedRetelling' => $completedRetelling,
            'contentMentalMaps' => array_values($contentMentalMaps),
            'userId' => $user->getId(),
        ]);
    }

    public function actionHistory()
    {
        $this->getView()->setMetaTags(
            'История просмотра',
            'История просмотра',
            'История просмотра',
            'История просмотра',
        );
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSearchModel(new UserStorySearch(Yii::$app->user->id), Yii::$app->request->queryParams)
                ->setEmptyText('В этом разделе будут отображаться истории, которые были просмотрены вами')
                ->setSearchAction(['/story/history'])
                ->asArray(),
        );
    }

    public function actionLiked()
    {
        $this->getView()->setMetaTags(
            'Понравившиеся истории',
            'Понравившиеся истории',
            'Понравившиеся истории',
            'Понравившиеся истории',
        );
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSearchModel(new StoryLikeSearch(Yii::$app->user->id), Yii::$app->request->queryParams)
                ->setEmptyText('В этом разделе будут отображаться понравившиеся вам истории')
                ->setSearchAction(['/story/liked'])
                ->asArray(),
        );
    }

    public function actionLike()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            throw new HttpException(403);
        }
        $model = new StoryLikeForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->likeService->rate($model->story_id, Yii::$app->user->id, $model->like);
            return [
                'success' => true,
                'like' => $this->likeService->getLikeCount($model->story_id),
                'dislike' => $this->likeService->getDislikeCount($model->story_id),
            ];
        }
        return ['success' => false];
    }

    public function actionAddFavorites($story_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            throw new HttpException(403);
        }
        $this->favoritesService->add(Yii::$app->user->id, $story_id);
        return ['success' => true];
    }

    public function actionFavorites()
    {
        $this->getView()->setMetaTags(
            'Избранные истории',
            'Избранные истории',
            'Избранные истории',
            'Избранные истории',
        );
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSearchModel(new StoryFavoritesSearch(Yii::$app->user->id), Yii::$app->request->queryParams)
                ->setEmptyText('В этом разделе будут отображаться истории, добавленные вами в избранное')
                ->setSearchAction(['/story/favorites'])
                ->asArray(),
        );
    }

    public function actionRandom()
    {
        $model = Story::oneRandomStory();
        return $this->redirect(['story/view', 'alias' => $model->alias]);
    }

    public function actionBedtimeStories()
    {
        $sectionModel = $this->findSectionModel('stories');
        $this->getView()->setMetaTags(
            'Сказки на ночь для детей',
            'Сказки на ночь для детей',
            'wikids, сказки, сказки на ночь, истории, каталог историй, сказки на ночь для детей',
            'Сказки на ночь для детей',
        );
        $searchModel = new StorySearch();
        $searchModel->category_id = $sectionModel->getSectionCategories();
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSectionModel($sectionModel)
                ->setSearchModel($searchModel, Yii::$app->request->queryParams)
                ->setSearchAction(['/story/index'])
                ->asArray(),
        );
    }

    public function actionAudioStories()
    {
        $sectionModel = $this->findSectionModel('stories');
        $this->getView()->setMetaTags(
            'Аудио сказки для детей',
            'Аудио сказки для детей',
            'Аудио сказки для детей, сказки, истории, wikids',
            'Аудио сказки для детей',
        );
        $searchModel = new StorySearch();
        $searchModel->category_id = $sectionModel->getSectionCategories();
        $searchModel->audio = 1;
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSectionModel($sectionModel)
                ->setSearchModel($searchModel, Yii::$app->request->queryParams)
                ->setSearchAction(['/story/index'])
                ->asArray(),
        );
    }

    public function actionMyaudio()
    {
        $this->getView()->setMetaTags('Моя озвучка', 'Моя озвучка', 'Моя озвучка', 'Моя озвучка');
        return $this->render(
            'index',
            (new StoryRenderParams())
                ->setSearchModel(new MyAudioStoriesSearch(Yii::$app->user->id), Yii::$app->request->queryParams)
                ->setEmptyText('В этом разделе будут отображаться истории, озвученные вами')
                ->setSearchAction(['/story/myaudio'])
                ->asArray(),
        );
    }

    public function actionChat(): string
    {
        $this->layout = "chat";

        if (class_exists(Module::class)) {
            $this->view->off(View::EVENT_END_BODY, [Module::getInstance(), 'renderToolbar']);
        }

        $chatForm = new GptChatForm();

        return $this->render("chat", [
            "formModel" => $chatForm,
        ]);
    }
}
