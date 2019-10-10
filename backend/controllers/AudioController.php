<?php


namespace backend\controllers;


use backend\models\audio\AudioUploadForm;
use backend\models\audio\CreateAudioForm;
use backend\models\audio\UpdateAudioForm;
use common\models\Story;
use common\models\StoryAudioTrack;
use common\rbac\UserRoles;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class AudioController extends Controller
{

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

    public function actionIndex(int $story_id)
    {
        $model = Story::findModel($story_id);
        $query = StoryAudioTrack::find();
        $query->andFilterWhere(['story_id' => $story_id]);
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
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(int $story_id)
    {
        $model = Story::findModel($story_id);

        $form = new CreateAudioForm();
        $form->story_id = $model->id;
        $form->user_id = Yii::$app->user->id;

        $audioUploadForm = new AudioUploadForm($model->id);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            $trackID = $form->createAudio();

            $audioUploadForm->audioTrackID = $trackID;
            $audioUploadForm->audioFiles = UploadedFile::getInstances($audioUploadForm, 'audioFiles');
            if ($audioUploadForm->upload()) {

            }

            return $this->redirect(['index', 'story_id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $form,
            'audioUploadForm' => $audioUploadForm,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $form = new UpdateAudioForm($id);
        $form->loadModel();

        $model = Story::findModel($form->story_id);

        $audioUploadForm = new AudioUploadForm($form->story_id);
        $audioUploadForm->audioTrackID = $form->model_id;

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            $form->saveAudio();

            $audioUploadForm->audioFiles = UploadedFile::getInstances($audioUploadForm, 'audioFiles');
            if ($audioUploadForm->upload()) {

            }

            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $form,
            'storyModel' => $model,
            'trackModel' => $form->getModel(),
            'audioUploadForm' => $audioUploadForm,
        ]);
    }

    public function actionDeleteFile(int $story_id, int $track_id, string $file)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new AudioUploadForm($story_id, $track_id);
        return ['success' => $form->deleteAudioFile($file)];
    }

    public function actionDelete(int $id)
    {
        $model = StoryAudioTrack::findModel($id);
        $model->delete();
        return $this->redirect(['index', 'story_id' => $model->story_id]);
    }

}