<?php

namespace frontend\controllers;

use common\components\JsonResponse;
use common\models\StoryTestQuestion;
use common\services\AnswerService;
use frontend\models\AnswerCheckForm;
use frontend\models\AnswerCreateHiddenForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class AnswerController extends Controller
{

    private $answerService;

    public function __construct($id, $module, AnswerService $answerService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->answerService = $answerService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCheck(): array
    {
        $this->response->format = Response::FORMAT_JSON;

        $form = new AnswerCheckForm();
        if ($form->load($this->request->post(), '') && $form->validate()) {

            $questionModel = StoryTestQuestion::find()
                ->with('storyTestAnswersWithHidden')
                ->where(['id' => $form->question_id])
                ->one();
            if ($questionModel === null) {
                throw new NotFoundHttpException('Вопрос не найден.');
            }

            $answers = array_combine(
                array_column($questionModel->storyTestAnswersWithHidden, 'id'),
                array_column($questionModel->storyTestAnswersWithHidden, 'name')
            );
            $levResult = $this->levenshteinCheck($form->answer, $answers);

            return (new JsonResponse())
                ->success()
                ->params([
                    'input' => $form->answer,
                    'output' => $levResult['output'],
                    'lev' => $levResult['lev'],
                ])
                ->asArray();
        }

        return (new JsonResponse())
            ->success(false)
            ->message('Validation error')
            ->asArray();
    }

    private function levOutput(string $match, int $lev): array
    {
        return ['output' => $match, 'lev' => $lev];
    }

    private function levenshteinCheck(string $input, array $words): array
    {

        $input = mb_strtolower($input);

        $shortest = -1;
        $match = '';

        foreach ($words as $word) {

            $lev = levenshtein($input, mb_strtolower(preg_replace('/[^A-Za-zА-яёЁ\d\s\']/u', '', $word)));

            if ($lev === 0) {
                $match = $word;
                $shortest = 0;
                break;
            }

            if ($lev < $shortest || $shortest < 0) {
                $match = $word;
                $shortest = $lev;
            }
            elseif ($lev === $shortest) {
                $match = $word;
            }
        }

        if ($shortest > 6) {
            return $this->levOutput('', $shortest);
        }

        return $this->levOutput($match, $shortest);
    }

    public function actionCreate(Response $response, Request $request): array
    {
        $response->format = Response::FORMAT_JSON;
        $form = new AnswerCreateHiddenForm();
        if ($form->load($request->post(), '')) {
            try {
                $answerModel = $this->answerService->createHidden($form);
                return (new JsonResponse())
                    ->success()
                    ->params(['answer' => $this->answerService->serializeModel($answerModel)])
                    ->asArray();
            }
            catch (\Exception $ex) {
                return (new JsonResponse())
                    ->success(false)
                    ->message($ex->getMessage())
                    ->asArray();
            }
        }
        return (new JsonResponse())
            ->success(false)
            ->message('No data')
            ->asArray();
    }
}
