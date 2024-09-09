<?php

declare(strict_types=1);

namespace backend\modules\changelog\controllers;

use backend\modules\changelog\ChangelogCreate\CreateAction;
use backend\modules\changelog\ChangelogList\ListAction;
use backend\modules\changelog\ChangelogUpdate\UpdateAction;
use backend\modules\changelog\models\Changelog;
use backend\modules\changelog\TagList\TagListAction;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use vova07\imperavi\actions\UploadFileAction;

class DefaultController extends Controller
{
    public function actions(): array
    {
        return [
            'index' => ListAction::class,
            'create' => CreateAction::class,
            'update' => UpdateAction::class,
            'image-upload' => [
                'class' => UploadFileAction::class,
                'url' => '/upload/', // Directory URL address, where files are stored.
                'path' => '@public/upload',
            ],
            'tags' => TagListAction::class,
        ];
    }

    /**
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function actionDelete(int $id): Response
    {
        $changelog = Changelog::findOne($id);
        if ($changelog === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        $changelog->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $changelog = Changelog::findOne($id);
        if ($changelog === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        return $this->renderPartial('view', ['changelog' => $changelog]);
    }
}
