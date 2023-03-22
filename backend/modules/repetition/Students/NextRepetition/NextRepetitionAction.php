<?php

declare(strict_types=1);

namespace backend\modules\repetition\Students\NextRepetition;

use common\models\UserStudent;
use Exception;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class NextRepetitionAction extends Action
{
    private $handler;

    public function __construct($id, $controller, Handler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(int $student_id, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $student = UserStudent::findOne($student_id);
        if ($student === null) {
            throw new NotFoundHttpException('Ученик не найден');
        }

        $startForm = new NextRepetitionForm([
            'student_id' => $student->id,
        ]);

        if ($startForm->load($request->post())) {

            if (!$startForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            try {
                $this->handler->handle($startForm);
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
