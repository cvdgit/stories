<?php

declare(strict_types=1);

namespace backend\SlideEditor\CopyRetelling;

use yii\base\Model;

class CopyRetellingForm extends Model
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
            'name' => 'Название пересказа',
            'id' => 'ИД пересказа',
            'slideId' => 'ИД слайда',
        ];
    }
}
