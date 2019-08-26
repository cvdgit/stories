<?php


namespace common\services;


use common\models\StoryTestQuestion;
use common\models\StoryTestResult;
use Yii;

class QuestionsService
{

    public function checkAnswer(int $questionID, string $userAnswer): bool
    {
        $question = StoryTestQuestion::findModel($questionID);
        $correctAnswers = $question->correctAnswersArray();
        sort($correctAnswers);
        $userAnswers = explode(',', $userAnswer);
        sort($userAnswers);
        return count($correctAnswers) === count($userAnswers) && implode(',', $correctAnswers) === implode(',', $userAnswers);
    }

    public function storeQuestionResult(int $story_id, int $question_id, int $correctAnswer): bool
    {
        $model = new StoryTestResult();
        $model->story_id = $story_id;
        $model->question_id = $question_id;
        $model->user_id = Yii::$app->user->id;
        $model->answer_is_correct = $correctAnswer;
        return $model->save();
    }

}