<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogUpdate;

use backend\modules\changelog\models\Changelog;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class UpdateAction extends Action
{
    private $handler;

    public function __construct($id, $controller, UpdateChangelogHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function run(int $id, Request $request): string
    {
        $changelog = Changelog::findOne($id);
        if ($changelog === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }

        $updateForm = new UpdateChangelogForm($changelog);
        if ($updateForm->load($request->post()) && $updateForm->validate()) {
            try {
                $this->handler->handle(new UpdateChangelogCommand($id, $updateForm->title, $updateForm->text, (int) $updateForm->status, $updateForm->tags));
                \Yii::$app->session->setFlash('success', 'Запись успешно обновлена');
            } catch (\Exception $exception) {
                \Yii::$app->errorHandler->logException($exception);
                \Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }

        return $this->controller->render('update', [
            'formModel' => $updateForm,
        ]);
    }
}
