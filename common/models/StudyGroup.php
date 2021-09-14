<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "study_group".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $createdBy
 * @property Story $story
 * @property User $updatedBy
 * @property StudyTaskAssign[] $studyTaskAssigns
 * @property StudyGroup[] $studyGroups
 * @property StudyTaskProgress[] $studyTaskProgresses
 * @property User[] $users
 */
class StudyGroup extends ActiveRecord
{

    public static function tableName()
    {
        return 'study_group';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'created_at' => 'Создана',
            'updated_at' => 'Изменена',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyGroupUsers()
    {
        return $this->hasMany(StudyGroupUser::class, ['study_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('study_group_user', ['study_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyTaskAssigns()
    {
        return $this->hasMany(StudyTaskAssign::class, ['study_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyTasks()
    {
        return $this->hasMany(StudyTask::class, ['id' => 'study_task_id'])
            ->viaTable('study_task_assign', ['study_group_id' => 'id']);
    }

    public static function updateRecord(int $id): void
    {
        self::updateAll(['updated_at' => time()], 'id = :id', [':id' => $id]);
    }

    public static function asArray(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }
}
