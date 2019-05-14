<?php

namespace frontend\controllers;

use common\rbac\UserPermissions;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\models\Story;
use frontend\models\StorySearch;
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
            'h1' => 'Каталог историй',
            'action' => ['/story/index'],
        ]);
    }

    /**
     * Displays a single Story model.
     * @param string $alias
     * @return mixed
     */
    public function actionView($alias)
    {
        $model = Story::findModelByAlias($alias);
        $dataProvider = Comment::getStoryComments($model->id);
        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('_comment_list', ['dataProvider' => $dataProvider]);
        }
        $model->updateCounters(['views_number' => 1]);
        $commentForm = new CommentForm($model->id);
        return $this->render('view', [
            'model' => $model,
            'userCanViewStory' => UserPermissions::canViewStory($model),
            'commentForm' => $commentForm,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $tag
     * @return string
     */
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
            'h1' => $model->name,
            'action' => ['/story/tag', 'tag' => $model->name],
        ]);
    }

    public function actionCategory($category)
    {
        $model = Category::findModelByAlias($category);
        $searchModel = new StorySearch();
        $searchModel->category_id = $model->id;
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
            'h1' => $model->name,
            'action' => ['/story/category', 'category' => $model->alias],
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

}