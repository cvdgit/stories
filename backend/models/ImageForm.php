<?php


namespace backend\models;


use common\models\StorySlide;
use yii\base\Model;

class ImageForm extends Model
{

    public $slide_id;
    public $collection_id;
    public $content_url;
    public $source_url;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slide_id'], 'required'],
            [['slide_id'], 'integer'],
            [['source_url', 'content_url', 'collection_id'], 'string', 'max' => 255],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
        ];
    }

}