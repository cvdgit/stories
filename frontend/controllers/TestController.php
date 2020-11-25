<?php

namespace frontend\controllers;

use common\helpers\UserHelper;
use common\models\Category;
use common\models\Story;
use frontend\models\StorySearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TestController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(int $category_id, $student_id = null)
    {
        $model = $this->findCategoryModel($category_id);

        $query = Story::findPublishedStories();
        $query->innerJoinWith(['categories', 'tests']);
        $query->andFilterWhere(['in', 'category.id', $model->subCategories()]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 40,
            ],
            'sort' => [
                'defaultOrder' => ['published_at' => SORT_DESC],
            ],
        ]);
        $this->getView()->setMetaTags('Тесты', 'Тесты', 'Тесты', 'Тесты');
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'students' => UserHelper::getStudents(),
            'activeStudent' => UserHelper::getStudent($student_id),
            'category' => $model,
        ]);
    }

    private function findCategoryModel(int $id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}