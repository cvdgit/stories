<?php

namespace backend\models;

use common\models\StorySlide;
use Yii;

/**
 * This is the model class for table "neo_slide_relations".
 *
 * @property int $slide_id
 * @property int $entity_id
 * @property int $relation_id
 *
 * @property StorySlide $slide
 */
class NeoSlideRelations extends \yii\db\ActiveRecord
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
    public function rules()
    {
        return [
            [['slide_id', 'entity_id', 'relation_id'], 'required'],
            [['slide_id', 'entity_id', 'relation_id'], 'integer'],
            [['slide_id', 'entity_id', 'relation_id'], 'unique', 'targetAttribute' => ['slide_id', 'entity_id', 'relation_id']],
            [['slide_id'], 'exist', 'skipOnError' => true, 'targetClass' => StorySlide::class, 'targetAttribute' => ['slide_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'slide_id' => 'Slide ID',
            'entity_id' => 'Entity ID',
            'relation_id' => 'Relation ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }
}
