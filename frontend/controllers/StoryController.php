<?php

namespace frontend\controllers;

use common\rbac\UserPermissions;
use common\services\story\CountersService;
use common\services\StoryFavoritesService;
use common\services\StoryLikeService;
use frontend\models\StoryFavoritesSearch;
use frontend\models\StoryLikeForm;
use frontend\models\StoryLikeSearch;
use frontend\models\UserStorySearch;
use Yii;
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
                'only' => ['add-comment', 'comment-list', 'history', 'liked', 'favorites'],
                'rules' => [
                    [
                        'actions' => ['add-comment', 'comment-list', 'history', 'liked', 'favorites'],
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

    public function __construct($id,
                                $module,
                                StoryService $storyService,
                                CountersService $countersService,
                                StoryLikeService $likeService,
                                StoryFavoritesService $favoritesService,
                                $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->countersService = $countersService;
        $this->likeService = $likeService;
        $this->favoritesService = $favoritesService;
    }

    public function actionIndex()
    {
        $this->getView()->setMetaTags(
            'Каталог историй',
            'Каталог историй',
            'wikids, сказки, истории, каталог историй',
            'Каталог историй'
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
     * @return mixed
     * @throws yii\web\NotFoundHttpException
     */
    public function actionView($alias)
    {
        $model = Story::findModelByAlias($alias);

        $dataProvider = Comment::getStoryComments($model->id);
        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('_comment_list', ['dataProvider' => $dataProvider]);
        }

        $this->countersService->updateCounters($model);

        $commentForm = new CommentForm($model->id);
        return $this->render('view', [
            'model' => $model,
            'userCanViewStory' => UserPermissions::canViewStory($model),
            'commentForm' => $commentForm,
            'dataProvider' => $dataProvider,
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

        $html = $model->body;
        if (!empty($filter)) {
            $document = \phpQuery::newDocumentHTML($model->body);
            $sections = $document->find('section')->elements;
            $slides = [];
            $slideFilter = $this->slideFilterArray($filter);
            foreach ($slideFilter as $slideIndex) {
                if (isset($sections[$slideIndex])) {
                    $slides[] = pq($sections[$slideIndex])->htmlOuter();
                }
            }
            $html = implode("\n", $slides);
        }
        return ['html' => $html];
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

}