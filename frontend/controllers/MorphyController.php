<?php

namespace frontend\controllers;

use cijic\phpMorphy\Morphy;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class MorphyController extends Controller
{

    public function behaviors()
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

    private function getBaseWord(Morphy $morphy, $word): ?string
    {
        if (is_numeric($word)) {
            return $word;
        }
        $matchPseudoRoot = $this->getPseudoRoot($morphy, $word);
        if (!empty($matchPseudoRoot)) {
            return $matchPseudoRoot;
        }
        return $this->getBaseForm($morphy, $word);
    }

    private function getPseudoRoot(Morphy $morphy, string $word): ?string
    {
        return $morphy->getPseudoRoot(mb_strtoupper($word))[0];
    }

    private function getBaseForm(Morphy $morphy, string $word): ?string
    {
        return $morphy->getBaseForm(mb_strtoupper($word))[0];
    }

    private function getBaseArray(Morphy $morphy, array $words)
    {
        return array_map(function($item) use ($morphy) {
            return $this->getBaseWord($morphy, $item);
        }, $words);
    }

    public function actionRoot()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $match = Yii::$app->request->post('match');
        $result = Yii::$app->request->post('result');

        $matchArray = explode(' ', $match);
        $resultArray = explode(' ', $result);

        /** TODO: переписать на свою обертку для phpMorphy */
        $morphy = new Morphy();

        $matchPseudoRoot = '';
        $resultPseudoRoot = '';
        if (count($matchArray) === 1) {
            $matchPseudoRoot = $this->getPseudoRoot($morphy, $match);
            $resultPseudoRoot = $this->getPseudoRoot($morphy, $result);
            $matchBaseForm = $this->getBaseForm($morphy, $match);
            $resultBaseForm = $this->getBaseForm($morphy, $result);
            if ((!empty($matchPseudoRoot) && !empty($resultPseudoRoot)) && ($matchPseudoRoot === $resultPseudoRoot)) {
                $result = $match;
            }
            else {
                if ((!empty($matchBaseForm) && !empty($resultBaseForm)) && $matchBaseForm === $resultBaseForm) {
                    $result = $match;
                }
            }
        }
        else {
            $baseMatchArray = $this->getBaseArray($morphy, $matchArray);
            $baseResultArray = $this->getBaseArray($morphy, $resultArray);
            $baseMatch = implode(' ', $baseMatchArray);
            $baseResult = implode(' ', $baseResultArray);
            $matchBaseForm = $baseMatch;
            $resultBaseForm = $baseResult;
            if (strcasecmp($baseMatch, $baseResult) === 0) {
                $result = $match;
            }
        }

        return [
            'result' => $result,
            'pseudo' => [
                'match' => $matchPseudoRoot,
                'result' => $resultPseudoRoot,
            ],
            'base' => [
                'match' => $matchBaseForm,
                'result' => $resultBaseForm,
            ]
        ];
    }

}