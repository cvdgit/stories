<?php


namespace backend\models\audio;


use common\models\Story;
use common\models\StoryAudioTrack;
use common\models\User;
use yii\base\Model;

class CreateAudioForm extends Model
{

    public $story_id;
    public $user_id;
    public $name;
    public $type;
    public $default;

    public function rules()
    {
        return [
            [['story_id', 'user_id', 'type', 'name'], 'required'],
            [['story_id', 'user_id', 'type', 'default'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'story_id' => 'История',
            'user_id' => 'Автор',
            'type' => 'Тип',
            'default' => 'По умолчанию',
            'name' => 'Заголовок',
        ];
    }

    public function createAudio()
    {
        $model = StoryAudioTrack::create($this->name, $this->story_id, $this->user_id, $this->type, $this->default);
        $model->save();
        return $model->id;
    }

}