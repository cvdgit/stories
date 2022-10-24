<?php

namespace backend\controllers;

use backend\forms\ContactRequestCommentForm;
use backend\services\ContactRequestService;
use common\models\ContactRequest;
use common\rbac\UserRoles;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\AjaxFilter;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class ContactRequestController extends Controller
{
    /**
     * @var ContactRequestService
     */
    private $contactRequestService;

    public function __construct($id, $module, ContactRequestService $contactRequestService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->contactRequestService = $contactRequestService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_CONTACT_REQUESTS],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            [
                'class' => AjaxFilter::class,
                'only' => ['delete'],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ContactRequest::find(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        if (($model = ContactRequest::findOne($id)) === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        $model->delete();
        return ['success' => true];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionComment(int $id, Request $request, Response $response)
    {
        if (($contact = ContactRequest::findOne($id)) === null) {
            throw new NotFoundHttpException('Запись не найдена');
        }
        $formModel = new ContactRequestCommentForm();
        $formModel->comment = $contact->comment;
        if ($formModel->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$formModel->validate()) {
                return ['success' => false, 'message' => 'Ошибка валидации'];
            }
            try {
                $this->contactRequestService->updateComment($contact->id, $formModel);
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return $this->renderAjax('_comment', [
            'formModel' => $formModel,
        ]);
    }
}
