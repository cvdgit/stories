<?php

declare(strict_types=1);

namespace backend\Testing\ImportQuestions\Questions;

use common\models\StoryTest;
use common\models\StoryTestQuestion;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class QuestionsAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $test_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $test = StoryTest::findOne($test_id);
        if ($test === null) {
            throw new NotFoundHttpException('Тест не найден');
        }
        return array_map(static function(StoryTestQuestion $question) {
            return [
                'id' => $question->id,
                'order' => $question->order,
                'name' => $question->name,
                'url' => Url::to(['/test/update-question', 'question_id' => $question->id])
            ];
        }, $test->storyTestQuestions);
    }
}
