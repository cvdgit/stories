<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Create;

use yii\base\Model;

class CreateImageGapsForm extends Model
{
    public $name;
    public $image;
    public $max_prev_items;

    public function rules(): array
    {
        return [
            [['name', 'image'], 'required'],
            ['name', 'string', 'max' => 256],
            ['image', 'image'],
            [['max_prev_items'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'image' => 'Изображение',
            'max_prev_items' => 'Возврат на',
        ];
    }

    public function getMaxPrevItems(): array
    {
        return [
            'Начало',
            '1 элемент',
            '2 элемента',
            '3 элемента',
            '4 элемента',
            '5 элементов',
        ];
    }
}
