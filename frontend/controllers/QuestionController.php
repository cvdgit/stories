<?php

namespace frontend\controllers;

use common\models\User;
use common\models\UserQuestionHistoryModel;
use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\rest\Controller;

class QuestionController extends Controller
{

    public function actionGet(int $questionId, int $questionsNumber, int $answersNumber)
    {
        $userHistory = [];
        $userStars = [];
        if (!Yii::$app->user->isGuest) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = Yii::$app->user->identity->getStudentID();
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistory($questionId);
            $userStars = $userQuestionHistoryModel->getUserQuestionHistoryStars($questionId);
        }
        $curl = new Curl();
        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams(['id' => $questionId, 'number' => $questionsNumber, 'answers' => $answersNumber])
            ->setPostParams(['history' => Json::encode($userHistory)])
            ->get(Yii::$app->params['neo.url'] . '/api/question/get');
        $result = Json::decode($result);

        $questions = [];
        $questionEntities = [];
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

            $stars = 0;
            if (isset($userStars[$resultItem['question_entity_id']])) {
                $stars = $userStars[$resultItem['question_entity_id']]['stars'];
            }

            $svg = $resultItem['question_svg'] ?? false;

            if (!in_array($resultItem['question_entity_id'], $questionEntities, true)) {
                $questionEntities[] = $resultItem['question_entity_id'];
            }

            $question = [
                'id' => $i,
                'name' => $resultItem['question'],
                'mix_answers' => 0,
                'type' => ((int)$resultItem['correct_number'] > 1 ? 1 : 0),
                'image' => $resultItem['question_image'],
                'storyTestAnswers' => $answers,
                'entity_id' => $resultItem['question_entity_id'],
                'entity_name' => $resultItem['question_entity_name'],
                'relation_id' => $resultItem['question_relation_id'],
                'relation_name' => $resultItem['question_relation_name'],
                'topic_id' => $resultItem['question_topic_id'],
                'topic_name' => $resultItem['question_topic_name'],
                'correct_number' => $resultItem['correct_number'],
                'stars' => [
                    'total' => 5,
                    'current' => $stars,
                ],
                'view' => $svg ? 'svg' : '',
                'svg' => $svg,
                'lastAnswerIsCorrect' => true,
            ];
            $questions[] = $question;
            $i++;
        }

        $progressCurrent = array_reduce($questions, function($carry, $item) use ($userStars) {
            if (isset($userStars[$item['entity_id']])) {
                $carry += $userStars[$item['entity_id']]['stars'];
            }
            return $carry;
        });

        return [0 => [
            'storyTestQuestions' => $questions,
            'test' => [
                'progress' => [
                    'total' => count($questions) * 5,
                    'current' => $progressCurrent,
                ]
            ],
            'students' => $this->getStudents(),
        ]];
    }

    protected function getStudents()
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
                ];
            }
        }
        return $students;
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

    public function actionAnswer()
    {
        if (Yii::$app->user->isGuest) {
            return ['success' => false];
        }
        $model = new UserQuestionHistoryModel();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $userQuestionHistoryID = $model->createUserQuestionHistory();
            $model->createUserQuestionAnswers($userQuestionHistoryID);
        }
        else {
            return $model->errors;
        }
        return ['success' => true];
    }

}