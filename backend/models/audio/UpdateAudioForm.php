<?php


namespace backend\models\audio;


use common\models\StoryAudioTrack;
use yii\base\Model;

class UpdateAudioForm extends Model
{

    public $name;
    public $type;
    public $default;

    public $story_id;
    public $user_id;

    public $model_id;
    private $_model;

    public function __construct(int $model_id, $config = [])
    {
        $this->model_id = $model_id;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['type', 'default'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'type' => 'Тип',
            'default' => 'По умолчанию',
            'name' => 'Заголовок',
        ];
    }

    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = StoryAudioTrack::findModel($this->model_id);
        }
        return $this->_model;
    }

    public function loadModel()
    {
        $model = $this->getModel();
        $this->story_id = $model->story_id;
        $this->user_id = $model->user_id;
        $this->name = $model->name;
        $this->type = $model->type;
        $this->default = $model->default;
    }

    public function saveAudio()
    {
        $model = $this->getModel();
        $model->name = $this->name;
        $model->type = $this->type;
        $model->default = $this->default;
        return $model->save();
    }

}