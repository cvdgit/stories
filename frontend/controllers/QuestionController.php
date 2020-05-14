<?php

namespace frontend\controllers;

use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\rest\Controller;

class QuestionController extends Controller
{

    public function actionGet(int $questionId, int $questionsNumber, int $answersNumber)
    {

        $curl = new Curl();
        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams(['id' => $questionId, 'number' => $questionsNumber, 'answers' => $answersNumber])
            ->get(Yii::$app->params['neo.url'] . '/api/question/get');
        $result = Json::decode($result);

        $questions = [];
        $i = 1;
        foreach ($result as $resultItem) {
            $answers = [];
            foreach ($resultItem['answers'] as $_answer) {
                $answer = [
                    'id' => $_answer['id'],
                    'name' => $_answer['answer'],
                    'is_correct' => $_answer['correct'] ? 1 : 0,
                    'image' => $_answer['image'],
                ];
                $answers[] = $answer;
            }
            $question = [
                'id' => $i,
                'name' => $resultItem['question'],
                'mix_answers' => 0,
                'type' => ((int)$resultItem['correct_number'] > 1 ? 1 : 0),
                'image' => $resultItem['question_image'],
                'storyTestAnswers' => $answers,
                'entity_id' => $resultItem['question_entity_id'],
                'relation_id' => $resultItem['question_relation_id'],
            ];
            $questions[] = $question;
            $i++;
        }

        return [0 => ['storyTestQuestions' => $questions]];
    }

    public function actionGetRelatedSlide(int $entity_id, int $relation_id)
    {
        return (new Query())
            ->select(['{{%story_slide}}.story_id AS story_id', '{{%story_slide}}.id AS slide_id'])
            ->from('{{%neo_slide_relations}}')
            ->innerJoin('{{%story_slide}}', '{{%story_slide}}.id = {{%neo_slide_relations}}.slide_id')
            ->where('{{%neo_slide_relations}}.entity_id = :entity', [':entity' => $entity_id])
            ->andWhere('{{%neo_slide_relations}}.relation_id = :relation', [':relation' => $relation_id])
            ->one();
    }

    public function actionAnswer(int $slide_id, int $entity_id, int $relation_id, int $answer_id)
    {

        return ['success' => true];
    }

}