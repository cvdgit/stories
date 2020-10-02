<?php

namespace frontend\controllers;

use backend\components\training\base\Serializer;
use backend\components\WordTestBuilder;
use common\models\StoryTest;
use common\models\TestWordList;
use common\models\UserQuestionHistoryModel;
use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Query;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class QuestionController extends Controller
{

    public function actionInit(int $testId)
    {
        $test = StoryTest::findModel($testId);
        return [
            'test' => [
                'header' => $test->header,
                'description' => $test->description_text,
                'remote' => $test->isRemote(),
            ],
            'students' => $this->getStudents($testId),
        ];
    }

    private function createQuestionParams($paramString)
    {
        $params = [];
        parse_str($paramString, $params);
        return $params;
    }

    private function decodeQueryResult($result)
    {
        if (empty($result)) {
            throw new HttpException(500, 'No data');
        }
        try {
            $result = Json::decode($result);
        }
        catch (\Exception $ex) {
            throw new HttpException(500, 'Incorrect JSON');
        }

        if (isset($result['type']) && mb_strtolower($result['type']) === 'error') {
            Yii::error($result, 'neo.load.test');
            throw new HttpException(500, 'Request error');
        }

        if (!isset($result['total'])) {
            Yii::error($result, 'neo.load.test');
            throw new HttpException(500, 'Request error');
        }

        return $result;
    }

    public function actionGet(int $testId, int $studentId = null, $question_params = null)
    {

        $test = StoryTest::findModel($testId);
        $questionId = $test->question_list_id;

        $userHistory = [];
        $userStars = [];
        $userStarsCount = 0;
        if ($studentId !== null) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $studentId;
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistory($test->id);
            $userStars = $userQuestionHistoryModel->getUserQuestionHistoryStars2($test->id);
            $userStarsCount = $userQuestionHistoryModel->getUserHistoryStarsCount($test->id);
        }

        if ($test->isSourceWordList()) {
            $wordListModel = $this->findWordListModel($test->word_list_id);
            $data = $wordListModel->getTestWordsAsArray($userHistory);
            $dataCount = $wordListModel->getTestWordsCount();
            $collection = (new WordTestBuilder($test, $data, $dataCount, $userStars))->build();
            return (new Serializer())->serialize($test, $collection, $this->getStudents($test->id), $userStarsCount);
        }

        $curl = new Curl();

        $params = ['id' => $questionId];
        if ($question_params !== null) {
            $params['params'] = $question_params;
        }

        if ($test->question_params !== null) {
            $params['params'] = base64_encode($test->question_params);
        }

        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams($params)
            ->setPostParams(['history' => Json::encode($userHistory)])
            ->get(Yii::$app->params['neo.url'] . '/api/question/get');

        $result = $this->decodeQueryResult($result);

        $numberQuestions = $result['total'];
        $incorrectAnswerAction = $result['incorrectAnswerAction'];

        $showAnswerImage = filter_var($result['showAnswerImage'], FILTER_VALIDATE_BOOLEAN);
        $showAnswerText = filter_var($result['showAnswerText'], FILTER_VALIDATE_BOOLEAN);
        $showQuestionImage = filter_var($result['showQuestionImage'], FILTER_VALIDATE_BOOLEAN);

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
                'test_id' => $test->id,
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
                'incorrectAnswerText' => $test->incorrect_answer_text,
                'showAnswerImage' => $showAnswerImage,
                'showAnswerText' => $showAnswerText,
                'showQuestionImage' => $showQuestionImage,
                'answerType' => 0,
            ],
            'students' => $this->getStudents($test->id),
            'incorrectAnswerAction' => $incorrectAnswerAction,
        ]];
    }

    protected function getStudents(int $testID)
    {
        $students = [];
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            foreach ($user->students as $student) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
                    'progress' => (int)$student->getProgress($testID),
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
            if ($model->isSourceNeo()) {
                $userQuestionHistoryID = $model->createUserQuestionHistory();
            }
            if ($model->isSourceWordList()) {
                $userQuestionHistoryID = $model->createWordListQuestionHistory();
            }
            $model->createUserQuestionAnswers($userQuestionHistoryID);
        }
        else {
            return $model->errors;
        }
        return ['success' => true];
    }

    /**
     * @param int $id
     * @return TestWordList|null
     * @throws NotFoundHttpException
     */
    protected function findWordListModel(int $id)
    {
        if (($model = TestWordList::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}