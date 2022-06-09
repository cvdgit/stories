<?php

namespace backend\models\question\sequence;

use common\models\StoryTestAnswer;
use DomainException;
use yii\base\Model;

class SequenceAnswerForm extends Model
{

    public $id;
    public $name;
    public $order;
    public $imagePath;
    public $type;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'max' => 50],
            ['type', 'in', 'range' => ['full', 'words']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Добавить новый ответ',
        ];
    }

    public static function create(string $name, int $order, string $imagePath = null, int $id = null): self
    {
        $model = new self();
        $model->id = $id;
        $model->name = $name;
        $model->order = $order;
        $model->imagePath = $imagePath;
        return $model;
    }

    public function hasImage(): bool
    {
        return !empty($this->imagePath);
    }

    public function createAnswer(int $questionID): void
    {
        if (!$this->validate()) {
            throw new DomainException('SequenceAnswerForm is not valid');
        }
        $model = StoryTestAnswer::createSequenceAnswer($questionID, $this->name);
        $model->save();

        $this->id = $model->id;
    }

    public function typeIsFull(): bool
    {
        return $this->type === 'full';
    }

    public function typeIsWords(): bool
    {
        return $this->type === 'words';
    }
}
