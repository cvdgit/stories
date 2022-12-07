<?php

namespace frontend\controllers;

use backend\components\training\base\Serializer;
use backend\components\training\collection\TestBuilder;
use backend\components\training\collection\WordTestBuilder;
use backend\services\QuizHistoryService;
use common\helpers\UserHelper;
use common\models\StoryTest;
use common\models\TestRememberAnswer;
use common\models\TestWordList;
use common\models\User;
use common\models\UserQuestionHistoryModel;
use common\models\UserStudent;
use Exception;
use frontend\services\QuestionProgressService;
use linslin\yii2\curl\Curl;
use Yii;
use yii\db\Query;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class QuestionController extends Controller
{
    private $quizHistoryService;

    /**
     * @var QuestionProgressService
     */
    private $questionProgressService;

    public function __construct($id, $module, QuizHistoryService $quizHistoryService, QuestionProgressService $questionProgressService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->quizHistoryService = $quizHistoryService;
        $this->questionProgressService = $questionProgressService;
    }

    public function actionInit(int $testId, int $userId = null, int $studentId = null): array
    {
        $test = StoryTest::findModel($testId);

        $students = [];
        if ($studentId === null) {

            $user = User::findOne($userId);
            if ($user === null && !Yii::$app->user->isGuest) {
                $user = Yii::$app->user->identity;
            }

            $students = UserHelper::getUserStudents($test, $user);
        }
        else {
            $student = UserStudent::findOne($studentId);
            if ($student !== null) {
                $students[] = [
                    'id' => $student->id,
                    'name' => $student->isMain() ? $student->user->getProfileName() : $student->name,
                    'progress' => (int) $student->getProgress($test->id),
                ];
            }
        }

        return [
            'test' => [
                'id' => $test->id,
                'header' => $test->header,
                'description' => HTMLPurifier::process(nl2br($test->description_text)),
                'remote' => $test->isRemote(),
            ],
            'students' => $students,
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
        catch (Exception $ex) {
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

    /**
     * @throws NotFoundHttpException
     * @throws HttpException
     */
    public function actionGet(int $testId, int $studentId = null, $question_params = null, bool $fastMode = false)
    {

        $test = $this->findTestModel($testId);
        $questionId = $test->question_list_id;

        $userHistory = [];
        $userStars = [];
        $userStarsCount = 0;

        $repeat = $test->calcRepeat($fastMode);

        if ($studentId !== null && !$fastMode) {
            $userQuestionHistoryModel = new UserQuestionHistoryModel();
            $userQuestionHistoryModel->student_id = $studentId;
            $userHistory = $userQuestionHistoryModel->getUserQuestionHistoryLocal($test->id, $repeat);
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

            $historyValues = [];
            if (count($userHistory) > 0 && $collection->count() > 0) {
                $questionId = $collection->getQuestions()[0]->getId();
                $lastQuestionOrder = (new Query())
                    ->select('order')
                    ->from('story_test_question')
                    ->where(['id' => $questionId])
                    ->scalar();
                $prevQuery = (new Query())
                    ->select('name')
                    ->from('story_test_question')
                    ->where(['story_test_id' => $test->id])
                    ->andWhere(['<', 'order', $lastQuestionOrder])
                    ->orderBy(['order' => SORT_ASC]);
                $historyValues = array_column($prevQuery->all(), 'name');
            }

            $serializer = new Serializer($historyValues);
            return $serializer->serialize($test, $collection, $this->getStudents($test->id), $userStarsCount, $fastMode);
        }

        if ($test->isSourceTests()) {

            $questions = [];
            $questionsTotal = 0;
            foreach ($test->relatedTests as $relatedTest) {
                $questionsTotal += $relatedTest->getQuestionDataCount();
                $questions = array_merge($questions, $relatedTest->getQuestionData($userHistory));
            }

            $collection = (new TestBuilder($test, $questions, $questionsTotal, $userStars, $fastMode))
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
                CURLOPT_TIMEOUT => 300,
                CURLOPT_CONNECTTIMEOUT => 300,
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

        $resultParams = $result['params'];
        $questionCode = $result['code'];

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
                'images' => $resultItem['question_images'],
                'storyTestAnswers' => $answers,
                'entity_id' => $resultItem['question_entity_id'],
                'entity_name' => $resultItem['question_entity_name'],
                'relation_id' => $resultItem['question_relation_id'],
                'relation_name' => $resultItem['question_relation_name'],
                'topic_id' => $resultItem['question_topic_id'],
                'topic_name' => $resultItem['question_topic_name'],
                'correct_number' => $resultItem['correct_number'],
                'stars' => [
                    'total' => $repeat,
                    'current' => (int)$stars,
                ],
                'view' => $svg ? 'svg' : '',
                'svg' => $svg,
                'lastAnswerIsCorrect' => true,
                'test_id' => $test->id,
                'answer_number' => $resultItem['answer_number'],
                'params' => $resultItem['params'] ?? [],
            ];
            $questions[] = $question;
        }

        return [0 => [
            'storyTestQuestions' => $questions,
            'test' => [
                'id' => $test->id,
                'progress' => [
                    'total' => $numberQuestions * $repeat,
                    'current' => (int)$userStarsCount,
                ],
                'incorrectAnswerText' => $test->incorrect_answer_text,
                'showAnswerImage' => $showAnswerImage,
                'showAnswerText' => $showAnswerText,
                'showQuestionImage' => $showQuestionImage,
                'answerType' => 0,
                'source' => $test->source,
                'repeatQuestions' => $repeat,
            ],
            'students' => $this->getStudents($test->id),
            'incorrectAnswerAction' => $incorrectAnswerAction,
            'params' => $resultParams,
            'code' => $questionCode,
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

    /**
     * @throws NotFoundHttpException
     */
    public function actionAnswer(): array
    {
        if (Yii::$app->user->isGuest) {
            return ['success' => false];
        }

        $model = new UserQuestionHistoryModel();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {

            $userQuestionHistoryID = null;
            if ($model->isSourceNeo()) {
                $userQuestionHistoryID = $model->createUserQuestionHistory();
            }
            if ($model->isSourceWordList() || $model->isSourceTest() || $model->isSourceTests()) {
                $userQuestionHistoryID = $model->createWordListQuestionHistory();
            }

            if ($userQuestionHistoryID !== null) {
                $createdModels = $model->createUserQuestionAnswers($userQuestionHistoryID);
                if ((count($createdModels) > 0) && $model->isSourceWordList()) {
                    $testModel = $this->findTestModel($model->test_id);
                    if ($testModel->isRememberAnswers()) {
                        TestRememberAnswer::updateTestRememberAnswer($testModel->id, $model->student_id, $model->entity_id, $createdModels[0]->answer_entity_name);
                    }
                }

                try {
                    $this->questionProgressService->saveProgress($model->student_id, $model->test_id, $model->progress, $model->question_topic_id);
                } catch (Exception $exception) {
                    Yii::$app->errorHandler->logException($exception);
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

    /**
     * @throws NotFoundHttpException
     */
    protected function findTestModel(int $id): StoryTest
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Тест не найден');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionQuizRestart(int $quiz_id, int $student_id): array
    {
        if (($quizModel = StoryTest::findOne($quiz_id)) === null) {
            throw new NotFoundHttpException('Тестирование не найдено');
        }
        if (($studentModel = UserStudent::findOne($student_id)) === null) {
            throw new NotFoundHttpException('Студент не найден');
        }

        /** @var User $currentUser */
        $currentUser = Yii::$app->user->identity;
        if (!$currentUser->isMyStudent($studentModel->id)) {
            throw new ForbiddenHttpException('Студент не принадлежит пользователю');
        }

        try {
            $this->quizHistoryService->clearHistory($quizModel->id, $studentModel->id);
            return ['success' => true];
        }
        catch (Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
