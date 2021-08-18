<?php

namespace backend\controllers\test;

use backend\components\BaseController;
use backend\models\AnswerImageUploadForm;
use backend\models\question\sequence\CreateSequenceQuestion;
use backend\models\question\sequence\SequenceAnswerForm;
use backend\models\question\sequence\UpdateSequenceQuestion;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

class QuestionSequenceController extends BaseController
{

    private $transactionManager;

    public function __construct($id, $module, TransactionManager $transactionManager, $config = [])
    {
        $this->transactionManager = $transactionManager;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionCreate(int $test_id)
    {
        /** @var StoryTest $testModel */
        $testModel = $this->findModel(StoryTest::class, $test_id);
        $model = new CreateSequenceQuestion($test_id);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->createQuestion();
                Yii::$app->session->setFlash('success', 'Вопрос успешно создан');
                return $this->redirect(['update', 'id' => $id]);
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }
        return $this->render('create', [
            'testModel' => $testModel,
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        /** @var StoryTestQuestion $model */
        $model = $this->findModel(StoryTestQuestion::class, $id);
        $form = new UpdateSequenceQuestion($model, $this->transactionManager);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $form->updateQuestion();
                Yii::$app->session->setFlash('success', 'Вопрос успешно изменен');
            }
            catch (\Exception $ex) {
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->refresh();
        }
        return $this->render('update', [
            'model' => $form,
            'testModel' => $model->storyTest,
            'errorText' => $model->getAnswersErrorText(),
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel(StoryTestQuestion::class, $id);
        $model->delete();
        return $this->redirect(['test/update', 'id' => $model->story_test_id]);
    }

    public function actionImage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new AnswerImageUploadForm();
        if ($form->load(Yii::$app->request->post())) {
            $form->answerImage = UploadedFile::getInstance($form, 'answerImage');
            if ($form->validate()) {
                /** @var StoryTestAnswer $answerModel */
                $answerModel = $this->findModel(StoryTestAnswer::class, $form->answer_id);
                if ($form->upload($answerModel->image)) {
                    $answerModel->image = $form->answerImage;
                    $answerModel->save();
                    return ['success' => true, 'image_path' => $answerModel->getImagePath()];
                }
            }
        }
        return ['success' => false];
    }

    public function actionCreateAnswer(int $question_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /** @var StoryTestQuestion $questionModel */
        $questionModel = $this->findModel(StoryTestQuestion::class, $question_id);
        $answerModel = new SequenceAnswerForm();
        if ($answerModel->load(Yii::$app->request->post())) {
            $answerModel->createAnswer($questionModel->id);
            return [
                'success' => true,
                'html' => $this->renderPartial('_answer_item', ['answerModel' => $answerModel]),
                'answer_id' => $answerModel->id,
            ];
        }
        return ['success' => false];
    }
}