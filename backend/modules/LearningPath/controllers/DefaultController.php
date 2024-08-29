<?php

declare(strict_types=1);

namespace backend\modules\LearningPath\controllers;

use backend\modules\LearningPath\Create\CreateForm;
use backend\modules\LearningPath\Create\CreateLearningPathCommand;
use backend\modules\LearningPath\Create\CreateLearningPathHandler;
use backend\modules\LearningPath\models\LearningPath;
use backend\modules\LearningPath\Update\UpdateNameForm;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class DefaultController extends Controller
{
    /**
     * @var CreateLearningPathHandler
     */
    private $createHandler;

    public function __construct($id, $module, CreateLearningPathHandler $createHandler, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->createHandler = $createHandler;
    }

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => LearningPath::find(),
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(Request $request, Response $response, WebUser $user)
    {
        $createModel = new CreateForm();
        if ($createModel->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$createModel->validate()) {
                return ["success" => false, "errors" => $createModel->getErrors()];
            }
            $payload = [];
            try {
                $this->createHandler->handle(new CreateLearningPathCommand(
                    $uuid = Uuid::uuid4(),
                    $createModel->name,
                    $payload,
                    $user->getId(),
                ));
                return $this->redirect(['learning-path/default/update', 'id' => $uuid->toString()]);
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                return ["success" => false, "errors" => $e->getMessage()];
            }
        }
        return $this->renderAjax('create', [
            'formModel' => $createModel,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id, Request $request): string
    {
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            throw new NotFoundHttpException('Карта знаний не найдена');
        }

        $updateNameForm = new UpdateNameForm([
            'name' => $learningPath->name,
        ]);

        $trees = array_keys($learningPath->payload);
        return $this->render('update', [
            'learningPath' => $learningPath,
            'trees' => $trees,
            'updateNameForm' => $updateNameForm,
        ]);
    }

    public function actionData(string $id, string $key, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            return ['success' => false, 'message' => 'Карта знаний не найдена'];
        }
        return $learningPath->payload[$key] ?? [];
        /*return [
            [
                'title' => 'Books',
                'expanded' => true,
                'folder' => true,
                'children' => [
                    [
                        'title' => 'The Hobbit',
                        'type' => 'book',
                        'author' => 'J.R.R. Tolkien',
                    ],
                ],
            ],
        ];*/
    }

    public function actionSave(string $id, Request $request, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            return ['success' => false, 'message' => 'Карта знаний не найдена'];
        }
        $payload = $request->post('payload');
        $learningPath->updatePayload($payload);
        if (!$learningPath->save()) {
            return ['success' => false, 'message' => 'Save exception'];
        }
        return ['success' => true];
    }

    public function actionSaveTreeName(string $id, Request $request, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            return ['success' => false, 'message' => 'Карта знаний не найдена'];
        }
        $payload = $request->post();
        $learningPath->updateTreeName($payload['tree'], $payload['name']);
        if (!$learningPath->save()) {
            return ['success' => false, 'message' => 'Save exception'];
        }
        return ['success' => true];
    }

    public function actionDeleteTree(string $id, Request $request, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            return ['success' => false, 'message' => 'Карта знаний не найдена'];
        }
        $payload = $request->post();
        $learningPath->deleteTree($payload['tree']);
        if (!$learningPath->save()) {
            return ['success' => false, 'message' => 'Save exception'];
        }
        return ['success' => true];
    }

    public function actionCreateTree(string $id, Request $request, Response $response)
    {
        $response->format = Response::FORMAT_JSON;
        $learningPath = LearningPath::findOne($id);
        if ($learningPath === null) {
            return ['success' => false, 'message' => 'Карта знаний не найдена'];
        }
        $payload = $request->post();
        $learningPath->createTree($payload['tree'], $payload['name']);
        if (!$learningPath->save()) {
            return ['success' => false, 'message' => 'Save exception'];
        }
        return ['success' => true];
    }
}
