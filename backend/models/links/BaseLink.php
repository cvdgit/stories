<?php

namespace backend\models\links;

use yii\base\Model;

class BaseLink extends Model
{

    public $slide_id;
    public $title;
    public $href;
    public $type;

    public function rules()
    {
        return [
            [['slide_id', 'title'], 'required'],
            [['slide_id'], 'integer'],
            [['title', 'href'], 'string', 'max' => 255],
            ['href', 'url'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Заголовок',
            'href' => 'Ссылка',
        ];
    }
}
