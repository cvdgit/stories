<?php

declare(strict_types=1);

namespace backend\SlideEditor\UpdateRetelling;

use backend\services\StoryEditorService;
use DomainException;
use Exception;
use frontend\Retelling\Retelling;
use Yii;
use yii\base\Action;
use yii\web\Request;
use yii\web\Response;

class UpdateRetellingAction extends Action
{
    /**
     * @var StoryEditorService
     */
    private $editorService;

    public function __construct($id, $controller, StoryEditorService $editorService, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->editorService = $editorService;
    }

    public function run(string $retelling_id, int $slide_id, string $block_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $retelling = Retelling::findOne($retelling_id);
        if ($retelling === null) {
            return ['success' => 'false', 'message' => 'Retelling not found'];
        }

        $updateForm = new UpdateRetellingForm();
        if ($updateForm->load($request->post(), '')) {
            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            try {

                $retelling->updateRetelling((int) $updateForm->with_questions, $updateForm->questions);
                if (!$retelling->save()) {
                    throw new DomainException('Retelling update error');
                }

                $html = $this->editorService->updateRetellingBlock($slide_id, $block_id, $retelling_id, $updateForm->required === '1');
                return ['success' => true, 'block_id' => $block_id, 'html' => $html];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return ['success' => false];
    }
}
