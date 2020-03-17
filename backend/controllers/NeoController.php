<?php


namespace backend\controllers;


use backend\models\NeoSlideRelations;
use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\rest\Controller;

class NeoController extends Controller
{

    public function actionEntityList()
    {
        $curl = new Curl();
        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->get('https://neo.test/api/entity/list');
        //die(var_dump($curl->errorText));
        return Json::decode($result);
    }

    public function actionRelationsList(int $entity_id)
    {
        $curl = new Curl();
        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams(['entity_id' => $entity_id])
            ->get('https://neo.test/api/relations/list');
        //die(var_dump($curl->errorText));
        return Json::decode($result);
    }

    public function actionRelatedEntitiesList(int $entity_id, int $relation_id)
    {
        $curl = new Curl();
        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams(['entity_id' => $entity_id, 'relation_id' => $relation_id])
            ->get('https://neo.test/api/entity/related');
        //die(var_dump($curl->errorText));
        return Json::decode($result);
    }

    public function actionSaveRelations()
    {
        $model = new NeoSlideRelations();
        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            $model->save();
        }
        return [];
    }

    public function actionDeleteRelation()
    {
        $post = Yii::$app->request->post();
        $model = NeoSlideRelations::findOne([
            'slide_id' => $post['slide_id'],
            'relation_id' => $post['relation_id'],
            'entity_id' => $post['entity_id'],
        ]);
        return $model->delete();
    }

}