<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "edu_topic".
 *
 * @property int $id
 * @property int $class_program_id
 * @property string $name
 * @property int $order
 *
 * @property EduClassProgram $classProgram
 * @property EduLesson[] $eduLessons
 */
class EduTopic extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_topic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class_program_id', 'name'], 'required'],
            [['class_program_id', 'order'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['class_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduClassProgram::className(), 'targetAttribute' => ['class_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_program_id' => 'Программа обучения',
            'name' => 'Название',
            'order' => 'Order',
        ];
    }

    public function getClassProgram(): ActiveQuery
    {
        return $this->hasOne(EduClassProgram::class, ['id' => 'class_program_id']);
    }

    public function getClassProgramArray(): array
    {
        $models = EduClassProgram::find()
            ->innerJoinWith(['class', 'program'])
            ->all();
        $map = array_map(static function($model) {
            return ['id' => $model->id, 'name' => $model->class->name . ' - ' . $model->program->name];
        }, $models);
        return array_combine(array_column($map, 'id'), array_column($map, 'name'));
    }

    public function getEduLessons(): ActiveQuery
    {
        return $this->hasMany(EduLesson::class, ['topic_id' => 'id'])
            ->orderBy(['edu_lesson.order' => SORT_ASC]);
    }

    public function getLessonsCount(): int
    {
        return $this->getEduLessons()->count();
    }
}
