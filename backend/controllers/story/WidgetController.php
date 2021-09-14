<?php

namespace backend\controllers\story;

use backend\components\BaseController;
use backend\components\SlideModifier;
use common\models\Story;
use common\models\StorySlide;
use Yii;
use yii\web\Response;

class WidgetController extends BaseController
{

    public function actions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::actions();
    }

    public function actionSlides(int $story_id)
    {
        /** @var Story $model */
        $model = $this->findModel(Story::class, $story_id);
        return array_map(static function(StorySlide $slide) {
            $data = (new SlideModifier($slide->id, $slide->data))
                ->addDescription()
                ->render();
            return [
                'id' => $slide->id,
                'slideNumber' => $slide->number,
                'data' => $data,
                'story' => $slide->isRelationPopulated('story') ? $slide->story->title : ''
            ];
        }, $model->getStorySlidesWidget());
    }
}
