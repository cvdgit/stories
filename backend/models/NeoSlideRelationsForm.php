<?php


namespace backend\models;


use common\models\StorySlide;
use yii\base\Model;

class NeoSlideRelationsForm extends Model
{

    public $slide_id;
    public $entity_id;
    public $relation_id;
    public $related_entity_id;
    public $entity_name;
    public $relation_name;
    public $related_entity_name;

    public function rules()
    {
        return [
            [['slide_id', 'entity_id', 'relation_id', 'related_entity_id'], 'required'],
            [['slide_id', 'entity_id', 'relation_id', 'related_entity_id'], 'integer'],
            [['entity_name', 'relation_name', 'related_entity_name'], 'string'],
            //[['entity_id', 'relation_id', 'related_entity_id'], 'unique', 'targetAttribute' => ['entity_id', 'relation_id', 'related_entity_id']],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'slide_id' => 'Слайд',
            'entity_id' => 'Сущность',
            'relation_id' => 'Связь',
            'related_entity_id' => 'Связанная сущность',
        ];
    }

    public function create()
    {
        if (!$this->validate()) {
            throw new \DomainException('Neo relation is not valid');
        }
        $model = NeoSlideRelations::create(
            $this->slide_id,
            $this->entity_id,
            $this->relation_id,
            $this->related_entity_id,
            $this->entity_name,
            $this->relation_name,
            $this->related_entity_name
        );
        $model->save();
    }

}