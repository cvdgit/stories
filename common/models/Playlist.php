<?php

namespace common\models;

use common\models\story\StoryStatus;
use DomainException;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "playlist".
 *
 * @property int $id
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Story[] $stories
 * @property Story[] $storiesAdmin
 */
class Playlist extends ActiveRecord
{

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

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
        ];
    }

    private function createStoriesCondition(): ActiveQuery
    {
        return $this
            ->hasMany(Story::class, ['id' => 'story_id'])
            ->viaTable('story_playlist', ['playlist_id' => 'id'])
            ->innerJoin('{{%story_playlist}}', '{{%story}}.id = {{%story_playlist}}.story_id')
            ->andWhere('{{%story_playlist}}.playlist_id = :playlist', [':playlist' => $this->id])
            ->orderBy(['-{{%story_playlist}}.order' => SORT_DESC, '{{%story_playlist}}.created_at' => SORT_ASC])
            ->select(['{{%story}}.*', '{{%story_playlist}}.order AS playlist_order']);
    }

    public function getStories(): ActiveQuery
    {
        return $this->createStoriesCondition()
            ->andWhere('{{%story}}.status = :status', [':status' => StoryStatus::PUBLISHED]);
    }

    public function getStoriesAdmin(): ActiveQuery
    {
        return $this->createStoriesCondition();
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

    public static function randomPlaylists(int $limit = 4)
    {
        $query = (new Query())
            ->select('t.id')
            ->distinct()
            ->from(['t' => self::tableName()])
            ->innerJoin(['t2' => '{{story_playlist}}'], 't.id = t2.playlist_id')
            ->innerJoin(['t3' => Story::tableName()], 't2.story_id = t3.id')
            ->where('t3.status = :status', [':status' => StoryStatus::PUBLISHED])
            ->limit($limit)
            ->orderBy('rand()');
        $ids = array_keys($query->indexBy('id')->all());
        if (count($ids) === 0) {
            return null;
        }
        return self::find()
            ->where(['in', 'id', $ids])
            ->all();
    }

    public static function deletePlaylistItem(int $playlistID, int $storyID)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete('{{%story_playlist}}', 'playlist_id = :playlist AND story_id = :story', [':playlist' => $playlistID, ':story' => $storyID]);
        return $command->execute();
    }
}
