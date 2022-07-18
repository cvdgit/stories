<?php

namespace modules\files\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "study_file".
 *
 * @property int $id
 * @property string|null $uuid
 * @property string $name
 * @property string $alias
 * @property int $folder_id
 * @property string $type
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 *
 * @property StudyFolder $folder
 */
class StudyFile extends ActiveRecord
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
        return 'study_file';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'name' => 'Название',
            'alias' => 'Alias',
            'folder_id' => 'Папка',
            'type' => 'Type',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
            'status' => 'Статус',
        ];
    }

    public function getFolder(): ActiveQuery
    {
        return $this->hasOne(StudyFolder::class, ['id' => 'folder_id']);
    }

    public static function create(string $uuid, string $name, int $folderId, string $type, string $alias = null, int $status = StudyFileStatus::STATUS_ACTIVE): self
    {
        $model = new self();
        $model->uuid = $uuid;
        $model->name = $name;
        $model->folder_id = $folderId;
        $model->type = $type;
        $model->alias = $alias;
        $model->status = $status;
        return $model;
    }

    private function getFileRoute(): array
    {
        return ['files/default/get', 'id' => $this->uuid];
    }

    public function getFileLink(): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl($this->getFileRoute());
    }

    public function getFileLinkBackend(): string
    {
        return Yii::$app->urlManagerFrontend->createAbsoluteUrl($this->getFileRoute());
    }

    public static function findForDownload(string $uuid): ?self
    {
        return self::find()
            ->where(['uuid' => $uuid])
            ->andWhere(['status' => StudyFileStatus::STATUS_ACTIVE])
            ->with(['folder'])
            ->one();
    }

    public function getFilePath(): string
    {
        return $this->folder->getFolderPath() . DIRECTORY_SEPARATOR . $this->uuid . '.' . $this->type;
    }

    public function getNameWithExtension(): string
    {
        return $this->name . '.' . $this->type;
    }

    public function updateFile(string $name, int $folderId, int $status, string $alias = null): void
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->folder_id = $folderId;
        $this->status = $status;
    }

    public function afterDelete()
    {
        $filePath = $this->getFilePath();
        if (file_exists($filePath)) {
            FileHelper::unlink($filePath);
        }
        parent::afterDelete();
    }

    public static function getFileUrlByAlias(string $alias): string
    {
        $model = self::find()
            ->where(['alias' => $alias])
            ->one();
        if ($model !== null) {
            return $model->getFileLink();
        }
        return '';
    }
}
