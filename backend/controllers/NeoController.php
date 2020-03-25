<?php


namespace backend\controllers;


use backend\models\NeoSlideRelations;
use linslin\yii2\curl\Curl;
use Yii;
use yii\helpers\Json;
use yii\rest\Controller;

class NeoController extends Controller
{

    protected function serviceCurl()
    {
        return (new Curl())
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]);
    }

    protected function serviceMethodUrl($method)
    {
        return Yii::$app->params['neo.url'] . $method;
    }

    public function actionEntityList($label_id = null)
    {
        $curl = $this->serviceCurl();
        if ($label_id !== null) {
            $curl->setGetParams(['label_id' => $label_id]);
        }
        $result = $curl->get($this->serviceMethodUrl('/api/entity/list'));
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
            ->get(Yii::$app->params['neo.url'] . '/api/relations/list');
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
            ->get(Yii::$app->params['neo.url'] . '/api/entity/related');
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

    public function actionQuestions(string $param, string $value)
    {
        $curl = new Curl();
        $result = $curl
            ->setHeader('Accept', 'application/json')
            ->setOptions([
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ])
            ->setGetParams(['param' => $param, 'value' => $value])
            ->get(Yii::$app->params['neo.url'] . '/api/question/');
        return Json::decode($result);
    }

    public function actionLabels()
    {
        $result = $this->serviceCurl()->get($this->serviceMethodUrl('/api/label/list'));
        return Json::decode($result);
    }

}