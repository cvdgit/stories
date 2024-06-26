<?php

declare(strict_types=1);

namespace backend\models\drag_words;

use yii\base\Model;

class CreateDragWordsForm extends Model
{
    public $name;
    public $content;
    public $payload;
    public $imageFile;

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
}
