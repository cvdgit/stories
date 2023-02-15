<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule\Update;

use backend\modules\repetition\Schedule\Schedule;
use backend\modules\repetition\Schedule\ScheduleForm;
use backend\modules\repetition\Schedule\ScheduleItemForm;
use yii\base\Action;
use yii\base\Model;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class UpdateAction extends Action
{
    private $handler;

    public function __construct($id, $controller, UpdateScheduleHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     * @return Response|string
     */
    public function run(int $id, Request $request)
    {
        $schedule = Schedule::findOne($id);
        if ($schedule === null) {
            throw new NotFoundHttpException('Расписание не найдено');
        }
        $updateForm = new ScheduleForm($schedule);

        if ($updateForm->load($request->post()) && $updateForm->validate()) {

            $items = [];
            foreach ($request->post('ScheduleItemForm') as $i => $rawModel) {
                $items[$i] = new ScheduleItemForm();
            }

            if (Model::loadMultiple($items, $request->post()) && Model::validateMultiple($items)) {
                try {
                    $this->handler->handle(new UpdateScheduleCommand($updateForm, $items));
                    \Yii::$app->session->setFlash('success', 'Расписание создано успешно');
                    return $this->controller->redirect(['index']);
                } catch (\Exception $exception) {
                    \Yii::$app->errorHandler->logException($exception);
                    \Yii::$app->session->setFlash('error', $exception->getMessage());
                    return $this->controller->refresh();
                }
            }
        }

        return $this->controller->render('update', [
            'formModel' => $updateForm,
            'itemFormModel' => new ScheduleItemForm(),
        ]);
    }
}
