<?php

namespace modules\edu\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "edu_class".
 *
 * @property int $id
 * @property string $name
 */
class EduClass extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'edu_class';
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
            'name' => 'Name',
        ];
    }
}
