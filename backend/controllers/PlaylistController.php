<?php


namespace backend\controllers;


use common\rbac\UserRoles;
use common\services\PlaylistService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class PlaylistController extends Controller
{

    protected $service;

    public function __construct($id, $module, PlaylistService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
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

    public function actionCreate(string $title)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->service->createPlaylist($title);
        return ['success' => true, 'playlist' => $model];
    }

}