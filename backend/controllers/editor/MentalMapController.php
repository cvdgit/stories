<?php

declare(strict_types=1);

namespace backend\controllers\editor;

use backend\components\BaseController;
use backend\components\story\MentalMapBlock;
use backend\components\story\MentalMapBlockContent;
use backend\components\story\reader\HtmlSlideReader;
use backend\MentalMap\MentalMap;
use backend\models\editor\MentalMapForm;
use backend\services\StoryEditorService;
use backend\SlideEditor\CreateMentalMapQuestions\UpdateMentalMapQuestionsForm;
use common\models\StorySlide;
use common\rbac\UserRoles;
use DomainException;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class MentalMapController extends BaseController
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
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionFragments(string $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $mentalMap = $this->findModel(MentalMap::class, $id);
        return [
            'success' => true,
            'items' => array_map(static function (array $item): array {
                return ['id' => $item['id'], 'text' => $item['text'] ?? $item['title']];
            }, $mentalMap->getItems()),
        ];
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionSourceFragments(int $slide_id, string $block_id, string $mental_map_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $slideModel = $this->findModel(StorySlide::class, $slide_id);

        $slide = (new HtmlSlideReader($slideModel->getSlideOrLinkData()))->load();
        /** @var MentalMapBlock $block */
        $block = $slide->findBlockByID($block_id);
        $content = MentalMapBlockContent::createFromHtml($block->getContent());

        $mentalMap = $this->findModel(MentalMap::class, $mental_map_id);
        $sourceMentalMap = $this->findModel(MentalMap::class, $mentalMap->source_mental_map_id);
        return [
            'success' => true,
            'items' => array_map(static function (array $item): array {
                return ['id' => $item['id'], 'text' => $item['text'] ?? $item['title']];
            }, $sourceMentalMap->getItems()),
            'questions' => $mentalMap->getQuestions(),
            'required' => $content->isRequired(),
        ];
    }

    public function actionUpdateQuestions(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $payload = Json::decode($request->rawBody);

        $updateForm = new UpdateMentalMapQuestionsForm();
        if ($updateForm->load($payload, '')) {
            if (!$updateForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $mentalMap = MentalMap::findOne($updateForm->mentalMapId);
            if ($mentalMap === null) {
                return ['success' => 'false', 'message' => 'Mental map not found'];
            }

            $updateMentalMapForm = new MentalMapForm();
            $updateMentalMapForm->slide_id = $updateForm->slideId;
            $updateMentalMapForm->block_id = $updateForm->blockId;
            $updateMentalMapForm->mental_map_id = $updateForm->mentalMapId;
            $updateMentalMapForm->required = $updateForm->required ? '1' : '0';

            try {
                $mentalMap->updateQuestions($updateForm->fragments);
                if (!$mentalMap->save()) {
                    throw new DomainException('MentalMap update error');
                }
                $html = $this->editorService->updateMentalMapBlock($updateMentalMapForm);
                return ['success' => true, 'block_id' => $updateForm->blockId, 'html' => $html];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }

        return ['success' => false];
    }
}
