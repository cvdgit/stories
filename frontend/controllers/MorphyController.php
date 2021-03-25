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

    public function actionRoot()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $match = Yii::$app->request->post('match');
        $result = Yii::$app->request->post('result');

        $morphy = new Morphy();

        $matchPseudoRoot = $morphy->getPseudoRoot(mb_strtoupper($match))[0];
        $resultPseudoRoot = $morphy->getPseudoRoot(mb_strtoupper($result))[0];

        $matchBaseForm = $morphy->getBaseForm(mb_strtoupper($match))[0];
        $resultBaseForm = $morphy->getBaseForm(mb_strtoupper($result))[0];

        if ((!empty($matchPseudoRoot) && !empty($resultPseudoRoot)) && ($matchPseudoRoot === $resultPseudoRoot)) {
            $result = $match;
        }
        else {
            if ((!empty($matchBaseForm) && !empty($resultBaseForm)) && $matchBaseForm === $resultBaseForm) {
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