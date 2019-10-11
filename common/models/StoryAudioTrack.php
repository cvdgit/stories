<?php

namespace common\models;

use backend\models\audio\AudioUploadForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "story_audio_track".
 *
 * @property int $id
 * @property int $story_id
 * @property int $user_id
 * @property int $type
 * @property int $default
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Story $story
 * @property User $user
 */
class StoryAudioTrack extends \yii\db\ActiveRecord
{

    const TYPE_ORIGINAL = 0;
    const TYPE_USER = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'story_audio_track';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => 'Story ID',
            'user_id' => 'Автор',
            'type' => 'Тип',
            'default' => 'По умолчанию',
            'name' => 'Заголовок',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStory()
    {
        return $this->hasOne(Story::class, ['id' => 'story_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(string $name, int $story_id, int $user_id, int $type, int $default): self
    {
        $model = new self();
        $model->name = $name;
        $model->story_id = $story_id;
        $model->user_id = $user_id;
        $model->type = $type;
        $model->default = $default;
        return $model;
    }

    public static function audioTypeArray(): array
    {
        return [
            self::TYPE_ORIGINAL => 'Оригинальная',
            self::TYPE_USER => 'Пользователя',
        ];
    }

    public function audioTypeValue()
    {
        $types = self::audioTypeArray();
        return $types[$this->type];
    }

    public static function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Аудио дорожка не найдена');
    }

    public function isDefault()
    {
        return (int)$this->default === 1;
    }

    public function afterDelete()
    {
        $form = new AudioUploadForm($this->story_id);
        $form->audioTrackID = $this->id;
        $form->deleteFiles();

        if ($this->isOriginal() && $this->isDefault()) {
            $command = Yii::$app->db->createCommand();
            $command->update('{{%story}}', ['audio' => 0], 'id = :storyID', [':storyID' => $this->story_id]);
            $command->execute();
        }

        parent::afterDelete();
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->isOriginal() && $this->isDefault()) {
            $command = Yii::$app->db->createCommand();
            $command->update('{{%story}}', ['audio' => 1], 'id = :storyID', [':storyID' => $this->story_id]);
            $command->execute();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function isOriginal()
    {
        return (int)$this->type === self::TYPE_ORIGINAL;
    }

    public function isUser()
    {
        return (int)$this->type === self::TYPE_USER;
    }

    public function isUserTrack($userID)
    {
        return (int)$this->type === self::TYPE_USER && $this->user_id === $userID;
    }

}
