<?php


namespace frontend\controllers;


use common\models\News;
use common\rbac\UserRoles;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class NewsController extends Controller
{

    public function actionIndex()
    {
        $this->getView()->setMetaTags(
            'Блог',
            'Блог',
            'блог, новости, wikids',
            'Блог'
        );

        $dataProvider = new ActiveDataProvider([
            'query' => News::find()->joinWith(['user.profile.profilePhoto'])->where(['news.status' => News::STATUS_PUBLISHED])->orderBy('created_at DESC'),
            'pagination' => ['pageSize' => 10],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($slug)
    {
        $model = $this->findModelBySlug($slug);
        $this->getView()->setMetaTags(
            $model->title,
            $model->title,
            $model->title
        );
        return $this->render('view', [
            'model' => $model,
            'displayModeratorButtons' => false, // \Yii::$app->user->can(UserRoles::PERMISSION_MANAGE_NEWS),
        ]);
    }

    /**
     * @param string $slug
     * @return null|News
     * @throws NotFoundHttpException
     */
    protected function findModelBySlug($slug)
    {
        if (($model = News::findOne(['slug' => $slug])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}