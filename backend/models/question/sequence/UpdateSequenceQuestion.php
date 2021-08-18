<?php

namespace backend\models\question\sequence;

use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\services\TransactionManager;
use DomainException;
use Yii;
use yii\helpers\Json;

class UpdateSequenceQuestion extends SequenceQuestion
{

    public $answers;
    public $sortable;

    private $model;
    private $transactionManager;

    public function __construct(StoryTestQuestion $model, TransactionManager $transactionManager, $config = [])
    {
        $this->model = $model;
        $this->transactionManager = $transactionManager;
        $this->loadModelAttributes();

        $answers = [];
        foreach ($this->model->storyTestAnswers as $answer) {
            $answers[] = SequenceAnswerForm::create($answer->name, $answer->order, $answer->getImagePath(), $answer->id);
        }
        $this->answers = $answers;

        parent::__construct($config);
    }

    public function rules()
    {
        return array_merge([
            [['answers', 'sortable'], 'safe'],
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

        if ($this->sortable !== null) {
            $answerIDs = explode(',', $this->sortable);
            if (count($answerIDs) > 0) {
                $command = Yii::$app->db->createCommand();
                $this->transactionManager->wrap(function () use ($command, $answerIDs) {
                    $order = 1;
                    foreach ($answerIDs as $answerID) {
                        $command->update(StoryTestAnswer::tableName(), ['order' => $order], 'id = :id', [':id' => $answerID]);
                        $command->execute();
                        $order++;
                    }
                });
            }
        }
    }

    public function getModel(): ?StoryTestQuestion
    {
        return $this->model;
    }
}
