<?php

namespace frontend\controllers;

use common\rbac\UserPermissions;
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
            'action' => ['/story/category', 'category' => $model->alias],
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

}