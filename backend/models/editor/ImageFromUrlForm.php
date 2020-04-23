<?php


namespace backend\models\editor;


use yii\base\Model;

class ImageFromUrlForm extends Model
{

    public $url;
    public $story_id;

    public function rules()
    {
        return [
            ['url', 'url'],
            ['story_id', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'url' => 'Ссылка на изображение',
        ];
    }

}