<?php

namespace common\models;

use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "playlist".
 *
 * @property int $id
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 *
 * @property StoryPlaylist[] $storyPlaylists
 * @property Story[] $stories
 */
class Playlist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'playlist';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return yii\db\ActiveQuery
     * @throws yii\base\InvalidConfigException
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['id' => 'story_id'])->viaTable('story_playlist', ['playlist_id' => 'id']);
    }

    public static function playlistsArray(): array
    {
        return array_map(function($item) {
            return ['url' => $item['id'], 'label' => $item['title']];
        }, self::find()->orderBy(['title' => SORT_ASC])->all());
    }

    public static function create(string $title): Playlist
    {
        $model = new self();
        $model->title = $title;
        return $model;
    }

    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new DomainException('Плейлист не найден.');
    }

}
