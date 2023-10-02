<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Create;

use common\models\StoryTest;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\UploadedFile;

class CreateAction extends Action
{
    /**
     * @var UploadImageGapsHandler
     */
    private $uploadHandler;
    /**
     * @var CreateImageGapsHandler
     */
    private $createQuestionHandler;

    public function __construct($id, $controller, UploadImageGapsHandler $uploadHandler, CreateImageGapsHandler $createQuestionHandler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->uploadHandler = $uploadHandler;
        $this->createQuestionHandler = $createQuestionHandler;
    }

    /**
     * @throws NotFoundHttpException
     * @return string|Response
     */
    public function run(int $test_id, Request $request)
    {
        $quiz = StoryTest::findOne($test_id);
        if ($quiz === null) {
            throw new NotFoundHttpException('Тест не найден');
        }

        $createForm = new CreateImageGapsForm();
        $createForm->name = 'Заполните пропуски на изображении';
        if ($createForm->load($request->post())) {
            $createForm->image = UploadedFile::getInstance($createForm, 'image');
            if (!$createForm->validate()) {
                return $this->controller->refresh();
            }

            $fileId = Uuid::uuid4()->toString();
            $rootFolder = Yii::getAlias('@public/test_images/image_gaps');
            $image = $fileId . '.' . $createForm->image->extension;

            try {
                $this->uploadHandler->handle(new UploadImageGapsCommand($fileId, $rootFolder, $createForm->image));
                $questionId = $this->createQuestionHandler->handle(new CreateQuestionCommand($quiz->id, $createForm->name, $image, $createForm->max_prev_items));
                return $this->controller->redirect(['update', 'id' => $questionId]);
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
                return $this->controller->refresh();
            }
        }

        return $this->controller->render('create', [
            'quizModel' => $quiz,
            'formModel' => $createForm,
        ]);
    }
}
