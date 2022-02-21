<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Story;
use api\modules\v1\models\StorySlide;
use backend\components\SlideModifier;
use common\models\slide\SlideKind;
use yii\rest\Controller;
use yii\web\Response;

class CourseController extends Controller
{

    public function actionView(int $id)
    {
        $this->response->format = Response::FORMAT_JSON;

        $course = Story::find()
            ->with('slides')
            ->where(['id' => $id])
            ->one();

        $lessons = [];
        foreach ($course->slides as $slide) {

            $slideData = $slide->data;
            if (SlideKind::isLink($slide)) {
                $slideData = StorySlide::findOne($slide->link_slide_id)->data;
            }

            $search = [
                'data-id=""',
                'data-background-color="#000000"',
            ];
            $replace = [
                'data-id="' . $this->id . '"',
                'data-background-color="#fff"',
            ];
            $slideData = str_replace($search, $replace, $slideData);

            $items = (new SlideModifier($slide->id, $slideData))
                ->addImageUrl()
                ->forLesson();

            if (count($items) > 0) {
                $lesson = [
                    'id' => $slide->id,
                    'title' => 'Слайд #' . $slide->id,
                    'type' => 'blocks',
                    'items' => $items,
                ];
                $lessons[] = $lesson;
            }
        }

        return ['course' => [
            'title' => $course->title,
            'id' => $course->id,
            'lessons' => $lessons,
        ]];
    }
}