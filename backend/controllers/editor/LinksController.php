<?php

namespace backend\controllers\editor;

use backend\models\links\CreateLink;
use backend\models\links\CreateYoutubeLink;
use backend\models\links\UpdateLinkForm;
use backend\models\links\UpdateYoutubeLink;
use common\models\StorySlide;
use common\models\StorySlideBlock;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class LinksController extends Controller
{

    public function actionIndex(int $slide_id)
    {
        $model = StorySlide::findSlide($slide_id);
        $query = StorySlideBlock::find();
        $query->andFilterWhere([
            'slide_id' => $slide_id,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(int $slide_id)
    {
        $model = new CreateLink($slide_id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createLink();
            Yii::$app->session->setFlash('success', 'Ссылка успешно создана');
            return $this->redirect(['index', 'slide_id' => $model->slide_id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = new UpdateLinkForm($id);
        $model->loadLink();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->saveLink();
            Yii::$app->session->setFlash('success', 'Ссылка успешно изменена');
            return $this->redirect(['index', 'slide_id' => $model->slide_id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = StorySlideBlock::findBlock($id);
        $model->delete();
        return $this->redirect(['index', 'slide_id' => $model->slide_id]);
    }

    public function actionYoutubeCreate(int $slide_id)
    {
        $model = new CreateYoutubeLink($slide_id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createLink();
            Yii::$app->session->setFlash('success', 'Ссылка успешно создана');
            return $this->redirect(['index', 'slide_id' => $model->slide_id]);
        }
        return $this->render('youtube_create', [
            'model' => $model,
        ]);
    }

    public function actionYoutubeUpdate(int $id)
    {
        $link = $this->findModel($id);
        $model = new UpdateYoutubeLink($link);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->updateLink();
            Yii::$app->session->setFlash('success', 'Ссылка успешно изменена');
            return $this->redirect(['index', 'slide_id' => $model->slide_id]);
        }
        return $this->render('youtube_update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the StorySlideBlock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StorySlideBlock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        if (($model = StorySlideBlock::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}