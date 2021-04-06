<?php

namespace frontend\controllers;

use backend\components\WordListFormatter;
use cijic\phpMorphy\Morphy;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class MorphyController extends Controller
{

    private $wordListFormatter;

    public function __construct($id, $module, WordListFormatter $wordListFormatter, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->wordListFormatter = $wordListFormatter;
    }

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

        $matches = [];
        $allMatchArray = [];
        if ($this->wordListFormatter->haveMatches($match, $matches) !== false) {
            for ($i = 1; $i <= 2; $i++) {
                $str = $match;
                $j = 0;
                foreach ($matches[0] as $key) {
                    $str = str_replace($key, $matches[$i][$j], $str);
                    $j++;
                }
                $allMatchArray[] = $str;
            }
        }
        else {
            $allMatchArray[] = $match;
        }

        $resultArray = explode(' ', $result);

        /** TODO: переписать на свою обертку для phpMorphy */
        $morphy = new Morphy();

        foreach ($allMatchArray as $matchItem) {

            $matchArray = explode(' ', $matchItem);

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
                    $result = $matchItem;
                }
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