<?php

namespace modules\files\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "study_file_history".
 *
 * @property int $id
 * @property int $user_id
 * @property int $study_file_id
 * @property int $created_at
 */
class StudyFileHistory extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'study_file_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'study_file_id'], 'required'],
            [['user_id', 'study_file_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'study_file_id' => 'Study File ID',
            'created_at' => 'Created At',
        ];
    }

    public static function create(int $userId, int $studyFileId): self
    {
        $model = new self();
        $model->user_id = $userId;
        $model->study_file_id = $studyFileId;
        return $model;
    }
}
