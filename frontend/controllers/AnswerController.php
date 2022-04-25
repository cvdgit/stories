<?php

namespace frontend\controllers;

use common\components\JsonResponse;
use common\models\StoryTestQuestion;
use frontend\models\AnswerCheckForm;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AnswerController extends Controller
{

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
                ->with('storyTestAnswers')
                ->where(['id' => $form->question_id])
                ->one();
            if ($questionModel === null) {
                throw new NotFoundHttpException('Вопрос не найден.');
            }

            $answers = array_combine(array_column($questionModel->storyTestAnswers, 'id'), array_column($questionModel->storyTestAnswers, 'name'));
            return (new JsonResponse())
                ->success()
                ->params([
                    'input' => $form->answer,
                    'output' => $this->levenshteinCheck($form->answer, $answers),
                ])
                ->asArray();
        }

        return (new JsonResponse())
            ->success(false)
            ->message('Validation error')
            ->asArray();
    }

    private function levenshteinCheck(string $input, array $words): string
    {
        $input = mb_strtolower($input);
        $shortest = -1;
        $match = [];
        foreach ($words as $word) {
            $lev = levenshtein($input, mb_strtolower(preg_replace('/[^A-Za-zА-яёЁ\d\s\']/u', '', $word)));
            //$lev = levenshtein($input, mb_strtolower(preg_replace('/[,\-\.!?]/u', '', $word)));
            if ($lev === 0) {
                $match = [$word];
                $shortest = 0;
                break;
            }
            if ($lev < $shortest || $shortest < 0) {
                $match = [$word];
                $shortest = $lev;
            } elseif ($lev === $shortest) {
                $match[] = $word;
            }
        }
        if ($shortest > 6) {
            return '';
        }
        return current($match);
    }
}
