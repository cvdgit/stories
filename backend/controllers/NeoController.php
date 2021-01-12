<?php

namespace backend\controllers;

use backend\models\NeoSlideRelations;
use backend\models\NeoSlideRelationsForm;
use common\models\StoryTest;
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

    public function actionRelatedEntitiesList(int $entity_id, int $relation_id, string $direction)
    {
        $result = $this->serviceCurl()
            ->setGetParams(['entity_id' => $entity_id, 'relation_id' => $relation_id, 'direction' => $direction])
            ->get($this->serviceMethodUrl('/api/entity/related'));
        return Json::decode($result);
    }

    public function actionCreateRelation()
    {
        $model = new NeoSlideRelationsForm();
        $result = ['success' => false, 'errors' => []];
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->create();
                $result['success'] = true;
            }
            catch (\Exception $ex) {
                if ($ex instanceof \DomainException) {
                    $result['errors'] = $model->errors;
                }
                else {
                    $result['errors'] = [$ex->getMessage()];
                }
            }
        }
        else {
            $result['errors'] = 'no';
        }
        return $result;
    }

    public function actionDeleteRelation()
    {
        $post = Yii::$app->request->post();
        $model = NeoSlideRelations::findOne([
            'slide_id' => $post['slide_id'],
            'entity_id' => $post['entity_id'],
            'relation_id' => $post['relation_id'],
            'related_entity_id' => $post['related_entity_id'],
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

    public function actionQuestionList()
    {
        $result = $this->serviceCurl()->get($this->serviceMethodUrl('/api/question/list'));
        return Json::decode($result);
    }

    public function actionQuestionGet(int $id, $animalID = null)
    {
        $test = StoryTest::findModel($id);
        $params = [
            'id' => $test->question_list_id
        ];
        if ($animalID !== null) {
            $params['animalID'] = $animalID;
        }
        $result = $this->serviceCurl()
            ->setGetParams($params)
            ->get($this->serviceMethodUrl('/api/question/get'));
        return Json::decode($result);
    }

    public function actionTaxonList()
    {
        $result = $this->serviceCurl()->get($this->serviceMethodUrl('/api/taxon/list'));
        return Json::decode($result);
    }

    public function actionTaxonValueList(string $taxon)
    {
        $result = $this->serviceCurl()
            ->setGetParams(['taxon' => $taxon])
            ->get($this->serviceMethodUrl('/api/taxon/values'));
        return Json::decode($result);
    }

    public function actionQuestionValues(int $id)
    {
        $result = $this->serviceCurl()
            ->setGetParams(['id' => $id])
            ->get($this->serviceMethodUrl('/api/question/values'));
        return Json::decode($result);
    }

}