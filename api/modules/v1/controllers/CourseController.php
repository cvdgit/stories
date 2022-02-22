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
            ->with('allSlides')
            ->where(['id' => $id])
            ->one();

        $lessons = [];

        $lessonIndex = 1;
        $currentLesson = null;

        $slides = $course->allSlides;
        foreach ($slides as $slide) {

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

            $slideItems = (new SlideModifier($slide->id, $slideData))
                ->addImageUrl()
                ->forLesson();

            $end = next($slides) === false;
            if (($currentLesson !== null && SlideKind::isQuiz($slide)) || $end) {
                if (count($currentLesson['items']) > 0) {
                    $lessons[] = $currentLesson;
                }
                $currentLesson = null;
                $lessonIndex++;
            }

            if ($currentLesson === null) {
                $currentLesson = [
                    'id' => $lessonIndex,
                    'title' => "Раздел $lessonIndex",
                    'type' => 'blocks',
                    'items' => [],
                ];
            }

            if (count($slideItems) > 0) {
                foreach ($slideItems as $item) {
                    $currentLesson['items'][] = $item;
                }
                $currentLesson['items'][] = [
                    'id' => 1,
                    'type' => 'divider',
                    'items' => [],
                ];
            }
        }

        foreach ($lessons as $key => $value) {
            $lastItem = end($value['items']);
            if ($lastItem['type'] === 'divider') {
                array_pop($lessons[$key]['items']);
            }
        }

        return ['course' => [
            'title' => $course->title,
            'id' => $course->id,
            'lessons' => $lessons,
        ]];
    }
}