<?php

declare(strict_types=1);

namespace frontend\controllers;

use Exception;
use frontend\ConsultRequest\ConsultRequestCommand;
use frontend\ConsultRequest\ConsultRequestForm;
use frontend\ConsultRequest\ConsultRequestHandler;
use Yii;
use yii\filters\AjaxFilter;
use yii\web\Controller;

use yii\web\Request;
use yii\web\Response;

class ConsultController extends Controller
{
    /**
     * @var ConsultRequestHandler
     */
    private $consultRequestHandler;

    public function __construct($id, $module, ConsultRequestHandler $consultRequestHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->consultRequestHandler = $consultRequestHandler;
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => AjaxFilter::class,
                'only' => ['request'],
            ],
        ];
    }

    public function actionRequest(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $requestForm = new ConsultRequestForm();
        if ($requestForm->load($request->post())) {
            if (!$requestForm->validate()) {
                return ['success' => false, 'message' => 'Валидация не пройдена'];
            }
            try {
                $this->consultRequestHandler->handle(new ConsultRequestCommand(
                    $requestForm->name,
                    $requestForm->email,
                    $requestForm->phone
                ));
                return ['success' => true, 'message' => 'Заявка успешно отправлена'];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => 'При создании заявки произошла ошибка. Попробуйте повторить позднее.'];
            }
        }
        return ['success' => false, 'message' => 'Нет данных'];
    }
}
