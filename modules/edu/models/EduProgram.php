<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_program".
 *
 * @property int $id
 * @property string $name
 *
 * @property EduClassBook[] $classBooks
 * @property EduClass[] $classes
 * @property EduClassBookProgram[] $eduClassBookPrograms
 * @property EduClassProgram[] $eduClassPrograms
 */
class EduProgram extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'edu_program';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
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
        ];
    }

    public function getClassBooks(): ActiveQuery
    {
        return $this->hasMany(EduClassBook::class, ['id' => 'class_book_id'])->viaTable('edu_class_book_program', ['program_id' => 'id']);
    }

    public function getClasses(): ActiveQuery
    {
        return $this->hasMany(EduClass::class, ['id' => 'class_id'])->viaTable('edu_class_program', ['program_id' => 'id']);
    }

    public function getEduClassBookPrograms(): ActiveQuery
    {
        return $this->hasMany(EduClassBookProgram::class, ['program_id' => 'id']);
    }

    public function getEduClassPrograms(): ActiveQuery
    {
        return $this->hasMany(EduClassProgram::class, ['program_id' => 'id']);
    }

    public function createTopicRoute(int $classId): array
    {
        $route = ['/edu/student/topic'];
        if (($classProgram = EduClassProgram::findClassProgram($classId, $this->id)) !== null && count($classProgram->eduTopics) > 0){
            $route['id'] = $classProgram->eduTopics[0]->id;
        }
        return $route;
    }
}
