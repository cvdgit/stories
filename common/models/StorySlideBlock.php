<?php

namespace common\models;

use backend\models\links\BlockType;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "story_slide_block".
 *
 * @property int $id
 * @property int $slide_id
 * @property int $type
 * @property string $title
 * @property string $href
 * @property int $created_at
 * @property int $updated_at
 *
 * @property StorySlide $slide
 */
class StorySlideBlock extends \yii\db\ActiveRecord
{

    const TYPE_BUTTON = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_slide_block';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slide_id', 'title'], 'required'],
            [['slide_id', 'type'], 'integer'],
            [['title', 'href'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slide_id' => 'Slide ID',
            'type' => 'Type',
            'title' => 'Заголовок',
            'href' => 'Ссылка',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlide()
    {
        return $this->hasOne(StorySlide::class, ['id' => 'slide_id']);
    }

    public static function create(int $slideID, string $title, string $href = '', int $type = BlockType::BUTTON): StorySlideBlock
    {
        $model = new self();
        $model->type = $type;
        $model->slide_id = $slideID;
        $model->title = $title;
        $model->href = $href;
        return $model;
    }

    public static function findBlock($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Блок не найден.');
    }

    public function getUpdateUrl(): string
    {
        $links = [
            BlockType::BUTTON => 'editor/links/update',
            BlockType::YOUTUBE => 'editor/links/youtube-update'
        ];
        return Url::to([$links[$this->type], 'id' => $this->id]);
    }

}
