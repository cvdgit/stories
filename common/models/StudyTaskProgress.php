<?php

namespace common\models;

use common\models\study_task\StudyTaskProgressStatus;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "study_task_progress".
 *
 * @property int $study_task_id
 * @property int $user_id
 * @property int $created_at
 * @property int $progress
 * @property int $status
 *
 * @property StudyTask $studyTask
 * @property User $user
 */
class StudyTaskProgress extends ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ]
        ];
    }

    public static function tableName()
    {
        return 'study_task_progress';
    }

/*    public function rules()
    {
        return [
            [['study_task_id', 'user_id'], 'required'],
            [['study_task_id', 'user_id', 'created_at', 'progress', 'status'], 'integer'],
            [['study_task_id', 'user_id'], 'unique', 'targetAttribute' => ['study_task_id', 'user_id']],
            [['study_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyTask::class, 'targetAttribute' => ['study_task_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }*/

    public function attributeLabels()
    {
        return [
            'study_task_id' => 'Study Task ID',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'progress' => 'Progress',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyTask()
    {
        return $this->hasOne(StudyTask::class, ['id' => 'study_task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function create(int $taskID, int $userID, int $status = StudyTaskProgressStatus::ASSIGNED): self
    {
        $model = new self;
        $model->study_task_id = $taskID;
        $model->user_id = $userID;
        $model->status = $status;
        $model->progress = 0;
        return $model;
    }
}
