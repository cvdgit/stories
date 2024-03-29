<?php

namespace common\models;

use common\helpers\Url;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "audio_file".
 *
 * @property int $id
 * @property string $name
 * @property string $folder
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
            [['name', 'folder', 'audio_file'], 'required'],
            [['name', 'folder', 'audio_file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Заголовок',
            'audio_file' => 'Audio File',
            'created_at' => 'Дата создания',
        ];
    }

    public function getStoryTestQuestions(): ActiveQuery
    {
        return $this->hasMany(StoryTestQuestion::class, ['audio_file_id' => 'id']);
    }

    public static function create(string $name, string $folder, string $audioFileName): self
    {
        $model = new self();
        $model->name = $name;
        $model->folder = $folder;
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

    public function getAudioFilePath(bool $url = false): ?string
    {
        if (!$this->haveAudioFile()) {
            return null;
        }
        return self::getAudioFilesPath($this->folder, $url) . '/' . $this->audio_file;
    }

    public function getAudioFileUrl(): ?string
    {
        if (!$this->haveAudioFile()) {
            return null;
        }
        return self::getAudioFilesPath($this->folder, true) . '/' . $this->audio_file;
    }

    public function afterDelete()
    {
        if (($path = $this->getAudioFilePath()) !== null && file_exists($path)) {
            FileHelper::unlink($path);
        }
        parent::afterDelete();
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }
}
