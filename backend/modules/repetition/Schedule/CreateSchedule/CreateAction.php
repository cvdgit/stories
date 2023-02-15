<?php

declare(strict_types=1);

namespace backend\modules\repetition\Schedule\CreateSchedule;

use backend\modules\repetition\Schedule\ScheduleForm;
use backend\modules\repetition\Schedule\ScheduleItemForm;
use yii\base\Action;
use yii\base\Model;
use yii\web\Request;
use yii\web\Response;

class CreateAction extends Action
{
    /** @var CreateScheduleHandler */
    private $handler;

    public function __construct($id, $controller, CreateScheduleHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response)
    {
        $createForm = new ScheduleForm();
        $createItemForm = new ScheduleItemForm();

        if ($createForm->load($request->post()) && $createForm->validate()) {

            $items = [];
            foreach ($request->post('ScheduleItemForm') as $i => $rawModel) {
                $items[$i] = new ScheduleItemForm();
            }

            if (Model::loadMultiple($items, $request->post()) && Model::validateMultiple($items)) {
                try {
                    $this->handler->handle(new CreateScheduleCommand($createForm, $items));
                    \Yii::$app->session->setFlash('success', 'Успешно');
                    return $this->controller->redirect(['index']);
                }
                catch (\Exception $ex) {
                    \Yii::$app->session->setFlash('error', $ex->getMessage());
                    return $this->controller->refresh();
                }
            }
        }

        return $this->controller->render('create', [
            'formModel' => $createForm,
            'itemFormModel' => $createItemForm,
        ]);
    }
}
