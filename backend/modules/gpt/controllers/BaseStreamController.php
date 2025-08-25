<?php

declare(strict_types=1);

namespace backend\modules\gpt\controllers;

use backend\modules\gpt\ChatEventStream;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Exception;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

abstract class BaseStreamController extends Controller
{
    /**
     * @var ChatEventStream
     */
    private $chatEventStream;

    public function __construct($id, $module, ChatEventStream $chatEventStream, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->chatEventStream = $chatEventStream;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        /*if ($action->id === "stream" || $action->id === "pdf" || $action->id === 'retelling' || $action->id === 'retelling-rewrite') {
            $this->enableCsrfValidation = false;
        }*/

        @ob_end_clean();
        ini_set('output_buffering', '0');
        set_time_limit(0);

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, must-revalidate');
        header('X-Accel-Buffering: no');
        header('Connection: keep-alive');

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->stream = true;
        $response->isSent = true;
        Yii::$app->session->close();

        return parent::beforeAction($action);
    }

    public function createFieldsPayload(string $content): array
    {
        $message = [
            'role' => 'user',
            'content' => $content,
        ];

        return [
            'input' => [
                'messages' => [
                    $message,
                ],
            ],
            'config' => [
                'metadata' => [
                    'conversation_id' => Uuid::uuid4()->toString(),
                ],
            ],
            'include_names' => [],
        ];
    }

    public function sendStream(string $target, string $fieldsJson): void
    {
        try {
            $this->chatEventStream->send(
                $target,
                Yii::$app->params['gpt.api.completions.host'],
                $fieldsJson,
            );
        } catch (Exception $ex) {
            Yii::$app->errorHandler->logException($ex);
        }
    }
}
