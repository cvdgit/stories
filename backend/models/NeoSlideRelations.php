<?php

namespace backend\models;

use common\models\StorySlide;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "neo_slide_relations".
 *
 * @property int $slide_id
 * @property int $entity_id
 * @property int $relation_id
 * @property int $related_entity_id
 * @property string $entity_name
 * @property string $relation_name
 * @property string $related_entity_name
 *
 * @property StorySlide $slide
 */
class NeoSlideRelations extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'neo_slide_relations';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'slide_id' => 'Слайд',
            'entity_id' => 'Сущность',
            'relation_id' => 'Связь',
            'related_entity_id' => 'Связанная сущность',
            'entity_name' => 'Имя сущности',
            'relation_name' => 'Имя отношения',
            'related_entity_name' => 'Имя связанной сущности',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    public static function create(int $slideID, int $entityID, int $relationID, int $relatedEntityID, string $entityName, string $relationName, string $relatedEntityName): NeoSlideRelations
    {
        $model = new self();
        $model->slide_id = $slideID;
        $model->entity_id = $entityID;
        $model->relation_id = $relationID;
        $model->related_entity_id = $relatedEntityID;
        $model->entity_name = $entityName;
        $model->relation_name = $relationName;
        $model->related_entity_name = $relatedEntityName;
        return $model;
    }

}
