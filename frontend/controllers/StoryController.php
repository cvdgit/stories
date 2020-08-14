<?php

namespace frontend\controllers;

use common\helpers\UserHelper;
use common\models\Playlist;
use common\models\StorySlide;
use common\models\StoryTest;
use common\rbac\UserPermissions;
use common\services\story\CountersService;
use common\services\StoryAudioService;
use common\services\StoryFavoritesService;
use common\services\StoryLikeService;
use common\services\QuestionsService;
use frontend\models\CreateStoryTestRun;
use frontend\models\MyAudioStoriesSearch;
use frontend\models\StoryFavoritesSearch;
use frontend\models\StoryLikeForm;
use frontend\models\StoryLikeSearch;
use frontend\models\StoryTrackModel;
use frontend\models\UserStorySearch;
use Yii;
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
use yii\web\Response;

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

    public function __construct($id,
                                $module,
                                StoryService $storyService,
                                CountersService $countersService,
                                StoryLikeService $likeService,
                                StoryFavoritesService $favoritesService,
                                QuestionsService $questionsService,
                                StoryAudioService $audioService,
                                $config = [])
    {
        $this->storyService = $storyService;
        $this->countersService = $countersService;
        $this->likeService = $likeService;
        $this->favoritesService = $favoritesService;
        $this->questionsService = $questionsService;
        $this->audioService = $audioService;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex()
    {
        $this->getView()->setMetaTags(
            'Истории для детей',
            'Истории для детей',
            'Истории для детей, wikids, сказки, истории',
            'Истории для детей'
        );
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/index'],
            'emptyText' => 'Список историй пуст',
        ]);
    }

    public function actionCategory($category)
    {
        $model = Category::findModelByAlias($category);
        $searchModel = new StorySearch();
        if ($model->sort_field !== null) {
            $searchModel->defaultSortField = $model->sort_field;
            $searchModel->defaultSortOrder = $model->sort_order ?? SORT_ASC;
        }
        $searchModel->category_id = $model->subCategories();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->getView()->setMetaTags(
            $model->name . ' - каталог историй',
            $model->name,
            'wikids, сказки, истории, каталог историй',
            $model->name
        );
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/category', 'category' => $model->alias],
            'emptyText' => 'Список историй пуст',
        ]);
    }

    public function actionTag($tag)
    {
        $model = Tag::findModelByName($tag);
        $searchModel = new StorySearch();
        $searchModel->tag_id = $model->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->getView()->setMetaTags(
            $model->name . ' - каталог историй',
            $model->name,
            'wikids, сказки, истории, каталог историй',
            $model->name
        );
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/tag', 'tag' => $model->name],
            'emptyText' => 'Список историй пуст',
        ]);
    }

    /**
     * Displays a single Story model.
     * @param string $alias
     * @param null $track_id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($alias, $track_id = null)
    {

        $playlistID = Yii::$app->request->get('list');
        $playlist = null;
        if ($playlistID !== null) {
            $playlist = Playlist::findModel((int)$playlistID);
        }

        $model = Story::findModelByAlias($alias);
        $dataProvider = Comment::getCommentDataProvider($model->id);
        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('_comment_list', ['dataProvider' => $dataProvider]);
        }

        $this->countersService->updateCounters($model);

        $commentForm = new CommentForm($model->id);
        return $this->render('view', [
            'model' => $model,
            'storyDefaultView' => $this->storyService->getDefaultStoryView(),
            'commentForm' => $commentForm,
            'dataProvider' => $dataProvider,
            'trackID' => $track_id,
            'playlist' => $playlist,
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
        }
        else {
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
            }
            else {
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

    public function actionGetStoryTest(int $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {

            $testRunModel = new CreateStoryTestRun();
            $testRunModel->test_id = $id;
            $testRunModel->student_id = UserHelper::getCurrentUserStudentID();
            $testRunModel->createStoryTestRun();
        }

        $json = StoryTest::find()->where('id = :id', [':id' => $id])->with('storyTestQuestions.storyTestAnswers')->asArray()->all();
        $json[0]['test']['progress'] = [
            'current' => 0,
            'total' => count($json[0]['storyTestQuestions']),
        ];
        $json[0]['students'] = $this->getStudents();
        return $json;
    }

    protected function getStudents()
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
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

    public function actionInitStoryPlayer(int $id)
    {
        if (Yii::$app->user->isGuest) {
            $model = Story::findModel(Yii::$app->params['story.needSignup.id']);
        }
        else {
            $model = Story::findModel($id);
            $userCanViewStory = UserPermissions::canViewStory($model);
            if (!$userCanViewStory) {
                $model = Story::findModel(Yii::$app->params['story.bySubscription.id']);
            }
        }

        $audioTrackPath = '';
        if ($model->isAudioStory() || $model->isUserAudioStory(Yii::$app->user->id)) {
            $track_id = Yii::$app->request->get('track_id');
            $track = $this->audioService->getStoryTrack($model, $track_id, Yii::$app->user->id);
            if ($track !== null) {
                $audioTrackPath = StoryTrackModel::getTrackRelativePath($model->id, $track->id);
            }
        }

        return $this->renderAjax('_player', [
            'model' => $model,
            'userCanViewStory' => true,
            'audioTrackPath' => $audioTrackPath,
            'playlistID' => Yii::$app->request->get('list'),
        ]);
    }

    public function actionHistory()
    {
        $this->getView()->setMetaTags('История просмотра', 'История просмотра', 'История просмотра', 'История просмотра');
        $searchModel = new UserStorySearch(Yii::$app->user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/history'],
            'emptyText' => 'В этом разделе будут отображаться истории, которые были просмотрены вами',
        ]);
    }

    public function actionLiked()
    {
        $this->getView()->setMetaTags('Понравившиеся истории', 'Понравившиеся истории', 'Понравившиеся истории', 'Понравившиеся истории');
        $searchModel = new StoryLikeSearch(Yii::$app->user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/liked'],
            'emptyText' => 'В этом разделе будут отображаться понравившиеся вам истории',
        ]);
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
        $this->getView()->setMetaTags('Избранные истории', 'Избранные истории', 'Избранные истории', 'Избранные истории');
        $searchModel = new StoryFavoritesSearch(Yii::$app->user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/favorites'],
            'emptyText' => 'В этом разделе будут отображаться истории, добавленные вами в избранное',
        ]);
    }

    public function actionRandom()
    {
        $model = Story::oneRandomStory();
        return $this->redirect(['story/view', 'alias' => $model->alias]);
    }

    public function actionBedtimeStories()
    {
        $this->getView()->setMetaTags(
            'Сказки на ночь для детей',
            'Сказки на ночь для детей',
            'wikids, сказки, сказки на ночь, истории, каталог историй, сказки на ночь для детей',
            'Сказки на ночь для детей'
        );
        $searchModel = new StorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/index'],
            'emptyText' => 'Список историй пуст',
        ]);
    }

    public function actionAudioStories()
    {
        $this->getView()->setMetaTags(
            'Аудио сказки для детей',
            'Аудио сказки для детей',
            'Аудио сказки для детей, сказки, истории, wikids',
            'Аудио сказки для детей'
        );
        $searchModel = new StorySearch();
        $searchModel->audio = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/index'],
            'emptyText' => 'Список историй пуст',
        ]);
    }

    public function actionMyaudio()
    {
        $this->getView()->setMetaTags('Моя озвучка', 'Моя озвучка', 'Моя озвучка', 'Моя озвучка');
        $searchModel = new MyAudioStoriesSearch(Yii::$app->user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'action' => ['/story/myaudio'],
            'emptyText' => 'В этом разделе будут отображаться истории, озвученные вами',
        ]);
    }

}