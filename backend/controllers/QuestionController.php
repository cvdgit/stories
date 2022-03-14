<?php

namespace backend\controllers;

use backend\models\audio_file\CreateAudioFileModel;
use backend\models\question\CreateRegionQuestion;
use backend\models\question\UpdateRegionQuestion;
use common\models\AudioFile;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\rbac\UserRoles;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class QuestionController extends Controller
{

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

    public function actionCreate(int $test_id, int $type)
    {
        $testModel = $this->findTestModel($test_id);
        $model = new CreateRegionQuestion();
        $model->test_id = $test_id;
        if ($model->load(Yii::$app->request->post())) {
            try {
                $id = $model->create();
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
        $model = $this->findModel($id);
        $form = new UpdateRegionQuestion($model);
        if ($form->load(Yii::$app->request->post())) {
            try {
                $form->update();
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
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['test/update', 'id' => $model->story_test_id]);
    }

    private function findModel($id)
    {
        if (($model = StoryTestQuestion::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function findAnswerModel($id)
    {
        if (($model = StoryTestAnswer::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function findTestModel($id)
    {
        if (($model = StoryTest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteAnswer($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findAnswerModel($id);
        $model->delete();
        return ['success' => true];
    }

    public function actionDeleteImage(int $id)
    {
        $model = $this->findModel($id);
        $fileDeleted = false;
        try {
            $model->deleteImage();
            $fileDeleted = true;
        }
        catch (\Exception $ex) {
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }

        $model->image = null;
        $model->save();
        Yii::$app->session->setFlash('success', 'Изображение успешно удалено');

        return $this->redirect(['test/update-question', 'question_id' => $model->id]);
    }

    public function actionCopy(int $id)
    {
        $question = $this->findModel($id);
        $copyQuestion = new StoryTestQuestion();
        $copyQuestion->attributes = $question->attributes;
        if ($copyQuestion->typeIsRegion()) {
            $copyQuestion->regions = null;
        }
        $copyQuestion->save();
        Yii::$app->session->addFlash('success', 'Вопрос успешно скопирован');
        return $this->redirect(['test/update-question', 'question_id' => $copyQuestion->id]);
    }

    public function actionPrint(int $test_id)
    {
        $testModel = $this->findTestModel($test_id);
        $questions = [];
        if ($testModel->isSourceTests()) {
            foreach ($testModel->relatedTests as $relatedTest) {
                $questions = array_merge($questions, $relatedTest->getQuestionData());
            }
        }
        else {
            $questions = $testModel->storyTestQuestions;
        }
        return $this->renderAjax('_print', [
            'testModel' => $testModel,
            'questions' => $questions,
        ]);
    }

    public function actionAutocomplete(string $query): array
    {
        $this->response->format = Response::FORMAT_JSON;
        return (new Query())
            ->select(['name', 'id'])
            ->from(AudioFile::tableName())
            ->where(['like', 'name', $query])
            ->orderBy(['name' => SORT_ASC])
            ->limit(30)
            ->all();
    }

    public function actionCreateAudioFile(): array
    {
        $this->response->format = Response::FORMAT_JSON;
        $model = new CreateAudioFileModel();
        if ($model->load($this->request->post())) {
            $model->audio_file = UploadedFile::getInstance($model, 'audio_file');
            try {
                $fileName = $model->uploadAudioFile();
                $audioFile = $model->createAudioFile($fileName);
                return ['success' => true, 'audio_file_id' => $audioFile->id, 'audio_file_name' => $audioFile->name];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'no data'];
    }
}
