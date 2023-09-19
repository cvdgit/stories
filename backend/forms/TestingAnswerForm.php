<?php

declare(strict_types=1);

namespace backend\forms;

use common\models\StoryTestAnswer;
use yii\base\Model;

class TestingAnswerForm extends Model
{
    public $name;
    public $is_correct;

    private $id = null;
    private $image = null;
    private $imagePath = null;

    public function __construct(StoryTestAnswer $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->is_correct = $model->is_correct;
            $this->image = $model->image;
            $this->imagePath = $model->getImageUrl();
        }
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['is_correct'], 'integer'],
            [['name'], 'string', 'max' => 512],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Ответ',
            'is_correct' => 'Ответ правильный',
        ];
    }

    public function haveImage(): bool
    {
        return $this->image !== null;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
