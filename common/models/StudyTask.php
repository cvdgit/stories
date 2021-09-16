<?php

namespace common\models;

use common\helpers\Url;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "study_task".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 * @property int $story_id
 * @property int $status
 *
 * @property User $createdBy
 * @property Story $story
 * @property User $updatedBy
 * @property StudyTaskAssign[] $studyTaskAssigns
 * @property StudyGroup[] $studyGroups
 * @property StudyTaskProgress[] $studyTaskProgresses
 * @property User[] $users
 */
class StudyTask extends ActiveRecord
{

    public static function tableName()
    {
        return 'study_task';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['title', 'story_id'], 'required'],
            [['description'], 'string'],
            [['created_by', 'updated_by', 'created_at', 'updated_at', 'story_id', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['story_id'], 'exist', 'skipOnError' => true, 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'description' => 'Описание',
            'created_by' => 'Создал',
            'updated_by' => 'Изменил',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
            'story_id' => 'История',
            'status' => 'Статус',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
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
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyTaskProgresses()
    {
        return $this->hasMany(StudyTaskProgress::class, ['study_task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('study_task_progress', ['study_task_id' => 'id']);
    }

    public static function create(string $title, int $storyID, int $status = 0, string $description = null): self
    {
        $model = new self();
        $model->title = $title;
        $model->description = $description;
        $model->story_id = $storyID;
        $model->status = $status;
        return $model;
    }

    public static function asArray(): array
    {
        return ArrayHelper::map(self::find()->orderBy(['title' => SORT_ASC])->all(), 'id', 'title');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyTaskAssigns()
    {
        return $this->hasMany(StudyTaskAssign::class, ['study_task_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudyGroups()
    {
        return $this->hasMany(StudyGroup::class, ['id' => 'study_group_id'])
            ->viaTable('study_task_assign', ['study_task_id' => 'id']);
    }

    public static function findTask(int $id): ?self
    {
        return self::findOne($id);
    }

    private function getStudyTaskRoute(): array
    {
        return ['study/task', 'id' => $this->id];
    }

    public function getStudyTaskUrl(): string
    {
        return Url::to($this->getStudyTaskRoute());
    }

    public function getStudyTaskUrlBackend(): string
    {
        return Yii::$app->urlManagerFrontend->createUrl($this->getStudyTaskRoute());
    }

    public function getUserProgress(int $userID)
    {
        return $this->getStudyTaskProgresses()
            ->andWhere('user_id = :user', [':user' => $userID])
            ->one();
    }
}
