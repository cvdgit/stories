<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "test_word".
 *
 * @property int $id
 * @property string $name
 * @property int $word_list_id
 * @property int $order;
 *
 * @property TestWordList $wordList
 */
class TestWord extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_word';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'word_list_id' => 'Word List ID',
            'order' => 'Порядок',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWordList()
    {
        return $this->hasOne(TestWordList::class, ['id' => 'word_list_id']);
    }

    public static function create(string $name, int $wordListID, int $order)
    {
        $model = new self();
        $model->name = $name;
        $model->word_list_id = $wordListID;
        $model->order = $order;
        return $model;
    }

}
