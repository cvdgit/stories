<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_program".
 *
 * @property int $id
 * @property string $name
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
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
        ];
    }
}
