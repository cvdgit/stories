<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Create;

use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Action;
use yii\web\Request;
use yii\web\UploadedFile;

class CreateFileAction extends Action
{
    private $handler;

    public function __construct($id, $controller, CreateFileHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request)
    {
        $createForm = new CreateFileForm();
        if ($createForm->load($request->post())) {
            $createForm->videoFile = UploadedFile::getInstance($createForm, 'videoFile');
            if ($createForm->validate()) {
                try {
                    $uuid = Uuid::uuid4()->toString();
                    $this->handler->handle(new CreateFileCommand($uuid, $createForm->title, $createForm->videoFile, $createForm->captions));
                    Yii::$app->session->addFlash('success', 'Видео успешно добавлено');
                    return $this->controller->redirect(['/video/file/index']);
                } catch (\Exception $exception) {
                    Yii::$app->errorHandler->logException($exception);
                    Yii::$app->session->addFlash('error', $exception->getMessage());
                }
            }
        }
        return $this->controller->render('create', [
            'model' => $createForm,
        ]);
    }
}
