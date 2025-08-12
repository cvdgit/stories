<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyMentalMap;

use yii\base\Model;

class CopyForm extends Model
{
    public $name;
    public $id;
    public $slideId;

    public function rules(): array
    {
        return [
            [['name', 'id', 'slideId'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['id'], 'string', 'max' => 36],
            ['slideId', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название ментальной карты',
            'id' => 'ИД ментальной карты',
            'slideId' => 'ИД слайда',
        ];
    }
}
