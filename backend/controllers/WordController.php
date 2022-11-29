<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\forms\WordForm;
use backend\services\WordService;
use common\models\TestWord;
use common\models\TestWordList;
use common\rbac\UserRoles;
use Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class WordController extends Controller
{
    /** @var WordService */
    private $wordService;

    public function __construct($id, $module, WordService $wordService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->wordService = $wordService;
    }

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
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate(int $list_id, Request $request, Response $response)
    {
        $wordList = $this->findListModel($list_id);
        $wordForm = new WordForm();
        if ($wordForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$wordForm->validate()) {
                return ['success' => false, 'message' => 'Word validation error'];
            }
            try {
                $this->wordService->create($wordList->id, $wordForm);
                return ['success' => true, 'message' => 'Слово успешно добавлено'];
            }
            catch (Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return $this->renderAjax('create', ['model' => $wordForm]);
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findModel(int $id): TestWord
    {
        if (($model = TestWord::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Слово не найдено');
    }

    /**
     * @throws NotFoundHttpException
     */
    private function findListModel(int $id): TestWordList
    {
        if (($model = TestWordList::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Список слов не найден');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id, Request $request, Response $response)
    {
        $word = $this->findModel($id);
        $wordForm = new WordForm($word);
        if ($wordForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$wordForm->validate()) {
                return ['success' => false, 'message' => 'Word validation error'];
            }
            try {
                $this->wordService->update($word->id, $wordForm);
                return ['success' => true, 'message' => 'Слово успешно изменено'];
            }
            catch (Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return $this->renderAjax('update', ['model' => $wordForm]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $this->findModel($id);
        try {
            $this->wordService->delete($id);
            return ['success' => true];
        } catch (Exception $exception) {
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCopy(int $id, Request $request, Response $response)
    {
        $word = $this->findModel($id);
        $copyForm = new WordForm($word);
        if ($copyForm->load($request->post())) {
            $response->format = Response::FORMAT_JSON;
            if (!$copyForm->validate()) {
                return ['success' => false, 'message' => 'Word validation error'];
            }
            try {
                $this->wordService->copy($word->word_list_id, $copyForm);
                return ['success' => true, 'message' => 'Слово успешно скопировано'];
            }
            catch (Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }
        return $this->renderAjax('copy', ['model' => $copyForm]);
    }
}
