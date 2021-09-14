<?php

namespace common\models;

use common\models\study_task\StudyTaskStatus;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "study_task_assign".
 *
 * @property int $study_task_id
 * @property int $study_group_id
 * @property int $created_at
 * @property int $created_by
 * @property int $expired_at
 *
 * @property StudyGroup $studyGroup
 * @property StudyTask $studyTask
 */
class StudyTaskAssign extends ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => null,
            ],
            [
                'class' => BlameableBehavior::class,
                'updatedByAttribute' => null,
            ]
        ];
    }

    public static function tableName()
    {
        return 'study_task_assign';
    }

    public function rules()
    {
        return [
            [['study_task_id', 'study_group_id', 'expired_at'], 'required'],
            [['study_task_id', 'study_group_id', 'created_at', 'created_by'], 'integer'],
            [['study_task_id', 'study_group_id'], 'unique', 'targetAttribute' => ['study_task_id', 'study_group_id']],
            [['study_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyGroup::class, 'targetAttribute' => ['study_group_id' => 'id']],
            [['study_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => StudyTask::class, 'targetAttribute' => ['study_task_id' => 'id']],
            [['expired_at'], 'datetime', 'format' => 'php:d.m.Y'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'study_task_id' => 'Задание',
            'study_group_id' => 'Группа',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'expired_at' => 'Выполнить задание до',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyGroup()
    {
        return $this->hasOne(StudyGroup::class, ['id' => 'study_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyTask()
    {
        return $this->hasOne(StudyTask::class, ['id' => 'study_task_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            StudyTaskStatus::setStatus($this->study_task_id, StudyTaskStatus::OPEN);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public static function taskAssignedToUser(StudyTask $task, User $user): void
    {
        if (!StudyTaskStatus::isOpen($task)) {
            throw new \DomainException('Задание не действующее');
        }
        $groupIDs = array_map(static function(StudyGroup $group) {
            return $group->id;
        }, $user->studyGroups);
        if (count($groupIDs) === 0) {
            throw new \DomainException('Пользователь не добавлен в группы');
        }
        $exists = (new Query())
            ->from(['t' => self::tableName()])
            ->where('t.study_task_id = :task', [':task' => $task->id])
            ->andWhere(['in', 't.study_group_id', $groupIDs])
            ->exists();
        if (!$exists) {
            throw new \DomainException('Задание не назначено пользователю');
        }
    }

    public function beforeSave($insert)
    {
        if (!is_int($this->expired_at)) {
            $this->expired_at = Yii::$app->formatter->asTimestamp($this->expired_at);
        }
        return parent::beforeSave($insert);
    }
}
