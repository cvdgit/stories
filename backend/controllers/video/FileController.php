<?php

declare(strict_types=1);

namespace backend\controllers\video;

use backend\models\video\UpdateFileVideoForm;
use backend\models\video\VideoSource;
use backend\VideoFromFile\Captions\VideoCaptionsAction;
use backend\VideoFromFile\Create\CreateFileAction;
use backend\VideoFromFile\Update\UpdateFileAction;
use backend\VideoFromFile\VideoListAction;
use common\models\SlideVideo;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FileController extends Controller
{
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions(): array
    {
        return [
            'index' => VideoListAction::class,
            'create' => CreateFileAction::class,
            'update' => UpdateFileAction::class,
            'captions' => VideoCaptionsAction::class,
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if (($model = SlideVideo::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Видео не найдено.');
    }

    public function actionDelete(int $id)
    {
        $model = $this->findModel($id);
        $model->delete();
        return $this->redirect(['video/index', 'source' => VideoSource::FILE]);
    }
}
