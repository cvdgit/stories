<?php


namespace backend\models;


use common\models\StorySlide;
use common\models\StorySlideImage;
use Yii;
use yii\base\Model;

class ImageForm extends Model
{

    public $slide_id;
    public $collection_account;
    public $collection_id;
    public $collection_name;
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
            [['source_url', 'content_url', 'collection_id', 'collection_name', 'collection_account'], 'string', 'max' => 255],
            [['collection_account'], 'in', 'range' => array_keys(Yii::$app->params['yandex.accounts'])],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
        ];
    }

    public function loadModel(int $id)
    {
        $this->model_id = $id;
        $model = $this->getModel();
        $this->source_url = $model->source_url;
        $this->collection_name = $model->collection_name;
    }

    private $_model;
    public $model_id;

    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = StorySlideImage::findModel($this->model_id);
        }
        return $this->_model;
    }

}