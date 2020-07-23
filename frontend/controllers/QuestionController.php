<?php

namespace frontend\controllers;

use common\models\UserQuestionHistoryModel;
use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\rest\Controller;

class QuestionController extends Controller
{

    public function actionInit($questionId)
    {
        $json = [
            'students' => $this->getStudents($questionId),
        ];
        return $json;
    }

    public function actionGet(int $questionId, int $questionsNumber, int $answersNumber, int $studentId = null)
    {
        $userHistory = [];
        $userStars = [];
        $userStarsCount = 0;
        if ($studentId !== null) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $studentId;
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistory($questionId);
            $userStars = $userQuestionHistoryModel->getUserQuestionHistoryStars2($questionId);
            $userStarsCount = $userQuestionHistoryModel->getUserHistoryStarsCount($questionId);
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
        $numberQuestions = $result['total'];
        $incorrectAnswerAction = $result['incorrectAnswerAction'];
        $result = $result['questions'];

        $questions = [];
        $i = 1;
        foreach ($result as $resultItem) {

            $answers = [];
            $correctAnswerIDs = [];
            foreach ($resultItem['answers'] as $_answer) {
                $description = $_answer['description'] ?? '';
                $answer = [
                    'id' => $_answer['id'],
                    'name' => $_answer['answer'],
                    'is_correct' => $_answer['correct'] ? 1 : 0,
                    'image' => $_answer['image'],
                    'description' => $description,
                ];
                if ($_answer['correct']) {
                    $correctAnswerIDs[] = $_answer['id'];
                }
                $answers[] = $answer;
            }

            $stars = 0;
            foreach ($userStars as $star) {
                if ((int)$star['entity_id'] === (int)$resultItem['question_entity_id'] && in_array((int)$star['answer_entity_id'], $correctAnswerIDs, true)) {
                    $stars = $star['stars'];
                    break;
                }
            }

            $svg = $resultItem['question_svg'] ?? false;

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
                    'current' => (int)$stars,
                ],
                'view' => $svg ? 'svg' : '',
                'svg' => $svg,
                'lastAnswerIsCorrect' => true,
            ];
            $questions[] = $question;
            $i++;
        }

        return [0 => [
            'storyTestQuestions' => $questions,
            'test' => [
                'progress' => [
                    'total' => $numberQuestions * 5,
                    'current' => (int)$userStarsCount,
                ],
            ],
            'students' => $this->getStudents($questionId),
            'incorrectAnswerAction' => $incorrectAnswerAction,
        ]];
    }

    protected function getStudents(int $questionId)
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
                    'progress' => (int)$student->getProgress($questionId),
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