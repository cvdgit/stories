<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Update;

use common\models\SlideVideo;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class UpdateFileAction extends Action
{
    /**
     * @var UpdateFileHandler
     */
    private $handler;

    public function __construct($id, $controller, UpdateFileHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(int $id, Request $request)
    {
        $video = SlideVideo::findOne($id);
        if ($video === null) {
            throw new NotFoundHttpException('Видео не найдено');
        }

        $updateForm = new UpdateFileForm($video);
        if ($updateForm->load($request->post()) && $updateForm->validate()) {
            try {
                $this->handler->handle(new UpdateFileCommand($updateForm->getId(), $updateForm->title, $updateForm->captions));
                Yii::$app->session->setFlash('success', 'Видео успешно изменено');
            }
            catch (\Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
            return $this->controller->refresh();
        }
        return $this->controller->render('update', [
            'model' => $updateForm,
        ]);
    }
}
