<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "slide_video".
 *
 * @property int $id
 * @property string $video_id
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 */
class SlideVideo extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'slide_video';
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
            [['created_at', 'updated_at'], 'integer'],
            [['video_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'video_id' => 'ИД видео Youtube',
            'title' => 'Название',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Updated At',
        ];
    }

    public static function videoArray(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['updated_at' => SORT_DESC])->all(), 'video_id', 'title');
    }

    public static function create(string $title, string $video_id): SlideVideo
    {
        $model = new self();
        $model->title = $title;
        $model->video_id = $video_id;
        return $model;
    }

    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Видео не найдено.');
    }

}
