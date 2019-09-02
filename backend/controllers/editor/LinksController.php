<?php


namespace backend\controllers\editor;


use backend\models\links\CreateLinkForm;
use backend\models\links\UpdateLinkForm;
use common\models\StorySlide;
use common\models\StorySlideBlock;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

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
        $model = new CreateLinkForm($slide_id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->createLink();
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
            return $this->refresh();
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

}