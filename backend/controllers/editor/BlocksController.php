<?php

namespace backend\controllers\editor;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use common\models\StorySlide;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class BlocksController extends Controller
{

    public function actionSave()
    {
        foreach (Yii::$app->request->post() as $item) {

            [$slideID, $blockID] = explode(':', $item['key']);

            $model = $this->findSlideModel($slideID);
            $slide = (new HtmlSlideReader($model->data))->load();

            $block = $slide->findBlockByID($blockID);
            if (isset($item['left'])) {
                $block->setLeft($item['left']);
            }
            if (isset($item['top'])) {
                $block->setTop($item['top']);
            }
            if (isset($item['width'])) {
                $block->setWidth($item['width']);
            }
            if (isset($item['height'])) {
                $block->setHeight($item['height']);
            }

            $html = (new HTMLWriter())->renderSlide($slide);
            $model->data = $html;
            $model->save(false);
        }

        return ['success' => true];
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findSlideModel($id)
    {
        if (($model = StorySlide::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}