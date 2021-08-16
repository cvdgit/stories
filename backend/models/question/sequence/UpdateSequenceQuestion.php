<?php

namespace backend\models\question\sequence;

use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use DomainException;
use yii\helpers\Json;

class UpdateSequenceQuestion extends SequenceQuestion
{

    public $answers;
    private $model;

    public function __construct(StoryTestQuestion $model, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        $this->loadModelAttributes();

        $answers = [];
        foreach ($this->model->storyTestAnswers as $answer) {
            $answers[] = [
                'id' => $answer->id,
                'text' => $answer->name,
                'order' => $answer->order,
            ];
        }
        $this->answers = Json::encode($answers);
    }

    public function rules()
    {
        return array_merge([
            ['answers', 'safe'],
        ], parent::rules());
    }

    private function loadModelAttributes(): void
    {
        foreach ($this->getAttributes() as $name => $value) {
            $modelAttributes = $this->model->getAttributes();
            if (isset($modelAttributes[$name])) {
                $this->{$name} = $this->model->{$name};
            }
        }
    }

    public function updateQuestion(): void
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $modelAttributes = $this->model->getAttributes();
        foreach ($this->getAttributes() as $name => $value) {
            if (array_key_exists($name, $modelAttributes)) {
                $this->model->{$name} = $value;
            }
        }
        $this->model->save();

        $currentAnswers = Json::decode($this->answers);
        foreach ($currentAnswers as $answer) {
            $id = $answer['id'] ?? null;
            $create = empty($id);
            if ($create) {
                $model = StoryTestAnswer::createSequenceAnswer($this->model->id, $answer['text'], $answer['order']);
            }
            else {
                $model = StoryTestAnswer::findModel($id);
                $model->name = $answer['text'];
                $model->order = $answer['order'];
            }
            $model->save();
        }
    }

    public function getModel(): ?StoryTestQuestion
    {
        return $this->model;
    }
}
