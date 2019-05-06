<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Story;
use common\models\StorySearch;
use common\models\Tag;
use common\models\Category;
use common\models\User;
use common\models\Comment;
use common\services\StoryService;
use frontend\models\CommentForm;

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
                'only' => ['add-comment', 'comment-list'],
                'rules' => [
                    [
                        'actions' => ['add-comment', 'comment-list'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    protected $storyService;

    public function __construct($id, $module, StoryService $storyService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new StorySearch();
        $searchModel->scenario = StorySearch::SCENARIO_FRONTEND;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Story model.
     * @param string $alias
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($alias)
    {
        $model = $this->findModelByAlias($alias);

        $dataProvider = Comment::getStoryComments($model->id);

        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('_comment_list', ['dataProvider' => $dataProvider]);
        }

        $model->updateCounters(['views_number' => 1]);

        $commentForm = new CommentForm($model->id);

        return $this->render('view', [
            'model' => $model,
            'userCanViewStory' => $this->storyService->userCanViewStory(
                $model,
                (Yii::$app->user->isGuest ? null : User::findOne(Yii::$app->user->id))
            ),
            'commentForm' => $commentForm,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModelByAlias($alias)
    {
        if (($model = Story::findStory(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    /**
     * @param $tag
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionTag($tag): string
    {
        $model = Tag::findOne(['name' => $tag]);
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }
        $searchModel = new StorySearch();
        $searchModel->scenario = StorySearch::SCENARIO_FRONTEND;
        $dataProvider = $searchModel->search(['StorySearch' => ['tag_id' => $model->id]]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCategory($category)
    {
        $model = Category::findOne(['alias' => $category]);
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }
        $searchModel = new StorySearch();
        $searchModel->scenario = StorySearch::SCENARIO_FRONTEND;
        $dataProvider = $searchModel->search(['StorySearch' => ['category_id' => $model->id]]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAddComment($id): string
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

}