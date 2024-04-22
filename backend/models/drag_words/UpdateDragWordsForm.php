<?php

declare(strict_types=1);

namespace backend\models\drag_words;

use common\models\StoryTestQuestion;
use yii\base\Model;

class UpdateDragWordsForm extends Model
{
    public $name;
    public $content;
    public $payload;
    public $imageFile;
    /**
     * @var StoryTestQuestion
     */
    private $model;

    public function __construct(StoryTestQuestion $model, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        $this->name = $model->name;
        $this->payload = $model->regions;
    }

    public function init(): void
    {
        $this->name = 'Расставьте слова по своим местам';
        parent::init();
    }

    public function rules(): array
    {
        return [
            [['name', 'content'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['imageFile'], 'image'],
            [['content', 'payload'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'content' => 'Текст с пропусками',
            'imageFile' => 'Изображение',
        ];
    }

    public function getImageUrl(): ?string
    {
        return $this->model->getImageUrl();
    }

    public function haveImage(): bool
    {
        return !empty($this->model->image);
    }

    public function getModelId(): int
    {
        return $this->model->id;
    }
}
