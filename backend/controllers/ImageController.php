<?php


namespace backend\controllers;


use common\models\StorySlideImage;
use common\rbac\UserRoles;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class ImageController extends Controller
{

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_STORIES],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $query = StorySlideImage::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

}