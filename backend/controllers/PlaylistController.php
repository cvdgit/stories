<?php


namespace backend\controllers;


use common\models\Playlist;
use common\rbac\UserRoles;
use common\services\PlaylistService;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
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

    public function actionIndex()
    {
        $query = Playlist::find();
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

    public function actionCreate(string $title)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->service->createPlaylist($title);
        return ['success' => true, 'playlist' => $model];
    }

    public function actionUpdate(int $id)
    {
        $model = Playlist::findModel($id);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionOrder(int $playlist_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Playlist::findModel($playlist_id);
        $stories = Yii::$app->request->post('stories');
        $command = Yii::$app->db->createCommand();
        $order = 1;
        foreach ($stories as $storyID) {
            $command->update('{{%story_playlist}}', ['order' => $order], 'playlist_id = :playlist AND story_id = :story', [':playlist' => $model->id, ':story' => (int)$storyID]);
            $command->execute();
            $order++;
        }
        return ['success' => true];
    }

}