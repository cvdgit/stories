<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogCreate;

use backend\modules\changelog\models\ChangelogStatus;
use Exception;
use Yii;
use yii\base\Action;
use yii\web\Request;

class CreateAction extends Action
{
    private $handler;

    public function __construct($id, $controller, CreateChangelogHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request)
    {
        $createForm = new CreateChangelogForm([
            'created' => Yii::$app->formatter->asDate(new \DateTime('now'), 'php:Y-m-d'),
            'status' => ChangelogStatus::DRAFT,
        ]);
        if ($createForm->load($request->post()) && $createForm->validate()) {
            try {
                $this->handler->handle($createForm);
                Yii::$app->session->setFlash('success', 'Запись успешно создана');
                return $this->controller->redirect(['index']);
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }
        return $this->controller->render('create', [
            'formModel' => $createForm,
        ]);
    }
}
