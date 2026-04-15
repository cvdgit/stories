<?php

declare(strict_types=1);

namespace backend\actions\SlideImport;

use common\models\Story;
use Exception;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class ImportAction extends Action
{
    /** @var ImportHandler */
    private $importHandler;

    public function __construct($id, $controller, ImportHandler $importHandler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->importHandler = $importHandler;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(int $story_id, Request $request, Response $response, WebUser $user)
    {
        $story = Story::findOne($story_id);
        if ($story === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $importForm = new SlideImportForm([
            'userId' => $user->getId(),
        ]);
        if ($importForm->load($request->post(), '')) {
            $response->format = Response::FORMAT_JSON;

            if (!$importForm->validate()) {
                return ['success' => false, 'message' => 'not valid'];
            }

            try {
                $this->importHandler->handle(
                    new SlidesImportCommand(
                        (int) $importForm->from_story_id,
                        (int) $importForm->to_story_id,
                        $user->getId(),
                        $importForm->slides,
                        null,
                        $importForm->delete_slides,
                    ),
                );
                return ['success' => true, 'message' => 'Слайды успешно импортированы'];
            } catch (Exception $exception) {
                return ['success' => false, 'message' => 'Импорт не удался по причине: ' . $exception->getMessage()];
            }
        }
        return $this->controller->renderAjax('import', [
            'storyId' => $story_id,
        ]);
    }
}
