<?php

declare(strict_types=1);

namespace backend\SlideEditor\TableOfContents\Image;

use yii\base\Model;

class CardImageForm extends Model
{
    public $card_id;
    public $image;

    public function rules(): array
    {
        return [
            [['card_id', 'image'], 'required'],
            [['card_id'], 'string'],
            ['image', 'image', 'extensions' => 'png, jpg, jpeg'],
        ];
    }
}
