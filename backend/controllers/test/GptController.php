<?php

declare(strict_types=1);

namespace backend\controllers\test;

use backend\Testing\Questions\Gpt\Create\CreateGptQuestionCommand;
use backend\Testing\Questions\Gpt\Create\CreateGptQuestionHandler;
use backend\Testing\Questions\Gpt\Create\GptQuestionCreateForm;
use backend\Testing\Questions\Gpt\Update\GptQuestionUpdateForm;
use backend\Testing\Questions\Gpt\Update\UpdateGptQuestionCommand;
use backend\Testing\Questions\Gpt\Update\UpdateGptQuestionHandler;
use common\models\StoryTest;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GptController extends Controller
{
    /**
     * @var CreateGptQuestionHandler
     */
    private $createGptQuestionHandler;
    /**
     * @var UpdateGptQuestionHandler
     */
    private $updateGptQuestionHandler;

    public function __construct($id, $module, CreateGptQuestionHandler $createGptQuestionHandler, UpdateGptQuestionHandler $updateGptQuestionHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->createGptQuestionHandler = $createGptQuestionHandler;
        $this->updateGptQuestionHandler = $updateGptQuestionHandler;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $test_id, Request $request)
    {
        $testing = StoryTest::findOne($test_id);
        if ($testing === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $createForm = new GptQuestionCreateForm();
        $createForm->name = 'Выполните задание';

        if ($createForm->load($request->post()) && $createForm->validate()) {

            $payload = Json::encode([
                'job' => $createForm->job,
                'promptId' => $createForm->promptId,
            ]);

            try {
                $this->createGptQuestionHandler->handle(new CreateGptQuestionCommand($testing->id, $createForm->name, $payload));
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return $this->redirect(['/test/update', 'id' => $testing->id]);
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }

        return $this->render('create', [
            'quizModel' => $testing,
            'formModel' => $createForm,
            'prompts' => ArrayHelper::map($this->getPrompts(), 'id', 'name'),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id, Request $request)
    {
        $questionModel = StoryTestQuestion::findOne($id);
        if ($questionModel === null) {
            throw new NotFoundHttpException('Вопрос не найден');
        }

        $quizModel = $questionModel->storyTest;
        $updateForm = new GptQuestionUpdateForm($questionModel);

        if ($updateForm->load($request->post()) && $updateForm->validate()) {

            $payload = Json::encode([
                'job' => $updateForm->job,
                'promptId' => $updateForm->promptId,
            ]);

            try {
                $this->updateGptQuestionHandler->handle(new UpdateGptQuestionCommand($quizModel->id, $questionModel->id, $updateForm->name, $payload));
                Yii::$app->session->setFlash('success', 'Вопрос успешно сохранен');
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }

        return $this->render('update', [
            'quizModel' => $quizModel,
            'formModel' => $updateForm,
            'questionModel' => $questionModel,
            'prompts' => ArrayHelper::map($this->getPrompts(), 'id', 'name'),
        ]);
    }

    private function getPrompts(): array
    {
        return (new Query())
            ->select([
                'id',
                'name',
            ])
            ->from(['t' => 'llm_prompt'])
            ->where("t.key = 'question'")
            ->orderBy(['t.name' => SORT_ASC])
            ->all();
    }
}
