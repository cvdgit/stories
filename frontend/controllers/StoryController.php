<?php

namespace frontend\controllers;

use Yii;
use common\models\Story;
use common\models\StorySearch;
use common\models\Tag;
use common\models\Category;
use yii\web\NotFoundHttpException;
use common\services\StoryService;
use common\service\CustomerPayment as PaymentService;

class StoryController extends \yii\web\Controller
{

    public $storyService;
    private $paymentService = null;

    public function __construct($id, $module, StoryService $storyService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->storyService = $storyService;
        $this->paymentService = new PaymentService();
    }

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
        $model->updateCounters(array('views_number' => 1));
        return $this->render('view', [
            'model' => $model,
            'userCanViewStory' => $this->storyService->userCanViewStory(
                $model,
                (Yii::$app->user->isGuest ? null : \common\models\User::findOne(Yii::$app->user->identity->id))
            ),
        ]);
    }

    /**
     * Finds the Story model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Story the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Story::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    protected function findModelByAlias($alias)
    {
        if (($model = Story::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Страница не найдена.');
    }

    public function actionViewByFrame($id)
    {
        $model = ($this->paymentService->availableRate($this->findModel($id))) ? $this->findModel($id) : null;
        return $this->renderPartial('frame', ['model' => $model]);
    }


    public function actionTag($tag)
    {
        $model = Tag::findOne(['name' => $tag]);
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }
        $searchModel = new StorySearch();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $model->getPublishedStories(),
        ]);
    }

    public function actionCategory($category)
    {
        $model = Category::findOne(['alias' => $category]);
        if ($model === null) {
            throw new NotFoundHttpException('Страница не найдена.');
        }
        $searchModel = new StorySearch();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $model->getPublishedStories(),
        ]);
    }

}