<?php

namespace backend\models\test;

use common\models\StoryTest;
use DomainException;

class UpdateForm extends BaseVariantModel
{

    private $model;

    public function __construct(StoryTest $model, $config = [])
    {
        $this->model = $model;
        $this->loadModelAttributes();
        $this->loadParamAttributes();
        $this->loadWrongParamAttributes();
        parent::__construct($config);
    }

    private function loadModelAttributes()
    {
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->{$name} = $this->model->{$name};
            }
        }
    }

    private function loadParamAttributes()
    {
        foreach (explode(';', $this->question_params) as $param) {
            [$paramName, $paramValue] = explode('=', $param);
            if (property_exists($this, $paramName)) {
                $this->{$paramName} = $paramValue;
            }
        }
    }

    private function loadWrongParamAttributes()
    {
        if (empty($this->wrong_answers_params)) {
            return;
        }
        foreach (explode('|', $this->wrong_answers_params) as $i => $param) {
            $taxonName = '';
            $taxonValue = '';
            foreach (explode(';', $param) as $paramItem) {
                [$name, $value] = explode('=', $paramItem);
                if ($name === 'taxonName') {
                    $taxonName = $value;
                }
                if ($name === 'taxonValue') {
                    $taxonValue = $value;
                }
            }
            $this->wrongAnswerTaxonNames[$i] = $taxonName;
            $this->wrongAnswerTaxonValues[$i] = $taxonValue;
        }
    }

    public function updateTestVariant()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->model->{$name} = $this->{$name};
            }
        }
        $this->model->question_params = sprintf('taxonName=%1s;taxonValue=%2s', $this->taxonName, $this->taxonValue);
        $this->model->wrong_answers_params = $this->createWrongAnswersParams();
        $this->model->save();
    }

}