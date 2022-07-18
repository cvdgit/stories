<?php

namespace modules\files\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "study_folder".
 *
 * @property int $id
 * @property string $name
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 * @property int $visible
 *
 * @property StudyFile[] $studyFiles
 */
class StudyFolder extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'study_folder';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 255],
            ['visible', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
            'title' => 'Заголовок',
            'visible' => 'Показывать',
        ];
    }

    public function getStudyFiles(): ActiveQuery
    {
        return $this->hasMany(StudyFile::class, ['folder_id' => 'id']);
    }

    public function getFolderPath(): string
    {
        return Yii::getAlias('@public/upload/' . $this->name);
    }

    public function getFolderFilesCount(): int
    {
        return $this->getStudyFiles()->count();
    }

    public function getActiveFiles(): array
    {
        return $this->getStudyFiles()
            ->andWhere(['status' => StudyFileStatus::STATUS_ACTIVE])
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }

    public static function findForLibrary(string $name): ?self
    {
        return self::find()
            ->where(['name' => $name])
            ->andWhere(['visible' => 1])
            ->one();
    }
}
