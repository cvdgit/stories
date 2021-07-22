<?php

namespace frontend\controllers;

use backend\components\training\base\Serializer;
use backend\components\training\collection\TestBuilder;
use backend\components\training\collection\WordTestBuilder;
use common\models\StoryTest;
use common\models\TestRememberAnswer;
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

    public function actionGet(int $testId, int $studentId = null, $question_params = null, bool $fastMode = false)
    {

        $test = $this->findTestModel($testId);
        $questionId = $test->question_list_id;

        $userHistory = [];
        $userStars = [];
        $userStarsCount = 0;
        if ($studentId !== null && !$fastMode) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $studentId;
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistoryLocal($test->id);
            $userStars = $userQuestionHistoryModel->getUserQuestionHistoryStarsLocal($test->id);
            $userStarsCount = $userQuestionHistoryModel->getUserHistoryStarsCountLocal($test->id);
        }

        if ($test->isSourceWordList()) {
            $wordListModel = $this->findWordListModel($test->word_list_id);
            $collection = (new WordTestBuilder($test, $wordListModel->getTestWordsData($test->id, $studentId, $userHistory), $wordListModel->getTestWordsCount(), $userStars, $fastMode))->build();
            return (new Serializer())->serialize(
                $test,
                $collection,
                $this->getStudents($test->id),
                $userStarsCount,
                $fastMode,
                $wordListModel->getLinkedStories());
        }

        if ($test->isSourceTest()) {
            $collection = (new TestBuilder($test, $test->getQuestionData($userHistory), $test->getQuestionDataCount(), $userStars, $fastMode))
                ->build();
            return (new Serializer())
                ->serialize($test, $collection, $this->getStudents($test->id), $userStarsCount, $fastMode);
        }

        if ($test->isSourceTests()) {

            $questions = [];
            foreach ($test->relatedTests as $relatedTest) {
                $questions = array_merge($questions, $relatedTest->getQuestionData());
            }

            $collection = (new TestBuilder($test, $questions, count($questions), $userStars, $fastMode))
                ->build();
            return (new Serializer())
                ->serialize($test, $collection, $this->getStudents($test->id), $userStarsCount, $fastMode);
        }

        $curl = new Curl();

        $params = ['id' => $questionId];
        if ($question_params !== null) {
            $params['params'] = $question_params;
        }

        if ($test->question_params !== null) {
            $params['params'] = base64_encode($test->question_params);
        }

        $postParams = [
            //'history' => Json::encode($userHistory),
            'wrong_params' => empty($test->wrong_answers_params) ? '' : urlencode(base64_encode($test->wrong_answers_params)),
        ];

        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams($params)
            ->setPostParams($postParams)
            ->get(Yii::$app->params['neo.url'] . '/api/question/get');

        $result = $this->decodeQueryResult($result);

        $numberQuestions = $result['total'];
        $incorrectAnswerAction = $result['incorrectAnswerAction'];

        $showAnswerImage = filter_var($result['showAnswerImage'], FILTER_VALIDATE_BOOLEAN);
        $showAnswerText = filter_var($result['showAnswerText'], FILTER_VALIDATE_BOOLEAN);
        $showQuestionImage = filter_var($result['showQuestionImage'], FILTER_VALIDATE_BOOLEAN);

        $result = $result['questions'];

        $questions = [];
        foreach ($result as $resultItem) {

            $questionID = (int)$resultItem['hash'];

            $skipQuestion = false;
            foreach ($userHistory as $history) {
                if ((int)$history['entity_id'] === $questionID) {
                    $skipQuestion = true;
                    break;
                }
            }
            if ($skipQuestion) {
                continue;
            }

            $answers = [];
            foreach ($resultItem['answers'] as $_answer) {
                $answer = [
                    'id' => $_answer['id'],
                    'name' => $_answer['answer'],
                    'is_correct' => $_answer['correct'] ? 1 : 0,
                    'image' => $_answer['image'],
                    'description' => $_answer['description'] ?? '',
                ];
                $answers[] = $answer;
            }

            $stars = 0;
            foreach ($userStars as $star) {
                if ((int)$star['entity_id'] === $questionID) {
                    $stars = $star['stars'];
                    break;
                }
            }

            $svg = $resultItem['question_svg'] ?? false;

            $question = [
                'id' => $questionID,
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
                    'total' => ($fastMode ? 1 : 5),
                    'current' => (int)$stars,
                ],
                'view' => $svg ? 'svg' : '',
                'svg' => $svg,
                'lastAnswerIsCorrect' => true,
                'test_id' => $test->id,
                'answer_number' => $resultItem['answer_number'],
            ];
            $questions[] = $question;
        }

        return [0 => [
            'storyTestQuestions' => $questions,
            'test' => [
                'id' => $test->id,
                'progress' => [
                    'total' => $numberQuestions * ($fastMode ? 1 : 5),
                    'current' => (int)$userStarsCount,
                ],
                'incorrectAnswerText' => $test->incorrect_answer_text,
                'showAnswerImage' => $showAnswerImage,
                'showAnswerText' => $showAnswerText,
                'showQuestionImage' => $showQuestionImage,
                'answerType' => 0,
                'source' => $test->source,
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
            if ($model->isSourceWordList() || $model->isSourceTest()) {
                $userQuestionHistoryID = $model->createWordListQuestionHistory();
            }
            $createdModels = $model->createUserQuestionAnswers($userQuestionHistoryID);

            if (count($createdModels) > 0) {
                if ($model->isSourceWordList()) {
                    $testModel = $this->findTestModel($model->test_id);
                    if ($testModel->isRememberAnswers()) {
                        TestRememberAnswer::updateTestRememberAnswer($testModel->id, $model->student_id, $model->entity_id, $createdModels[0]->answer_entity_name);
                    }
                }
            }
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

    protected function findTestModel(int $id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}