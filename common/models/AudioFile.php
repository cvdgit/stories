<?php

namespace common\models;

use common\helpers\Url;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "audio_file".
 *
 * @property int $id
 * @property string $name
 * @property string $audio_file
 * @property int $created_at
 *
 * @property StoryTestQuestion[] $storyTestQuestions
 */
class AudioFile extends ActiveRecord
{

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'audio_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'audio_file'], 'required'],
            [['name', 'audio_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'audio_file' => 'Audio File',
            'created_at' => 'Created At',
        ];
    }

    public function getStoryTestQuestions(): ActiveQuery
    {
        return $this->hasMany(StoryTestQuestion::class, ['audio_file_id' => 'id']);
    }

    public static function create(string $name, string $audioFileName): self
    {
        $model = new self();
        $model->name = $name;
        $model->audio_file = $audioFileName;
        return $model;
    }

    private static function getAudioFilesRootPath(bool $url = false): string
    {
        return ($url ? Url::homeUrl() : Yii::getAlias('@public')) . '/' . Yii::$app->params['folder.question_audio'];
    }

    public static function getAudioFilesPath(string $folder, bool $url = false): string
    {
        return self::getAudioFilesRootPath($url) . '/' . $folder;
    }

    public function updateAudioFile(string $fileName = null): void
    {
        $this->audio_file = $fileName;
    }

    public function haveAudioFile(): bool
    {
        return !empty($this->audio_file);
    }

    public function getAudioFilePath(string $folder): ?string
    {
        if (!$this->haveAudioFile()) {
            return null;
        }
        return self::getAudioFilesPath($folder) . '/' . $this->audio_file;
    }

    public function getAudioFileUrl($folder): ?string
    {
        if (!$this->haveAudioFile()) {
            return null;
        }
        return self::getAudioFilesPath($folder, true) . '/' . $this->audio_file;
    }
}
