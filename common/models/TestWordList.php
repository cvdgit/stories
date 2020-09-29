<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "test_word_list".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property TestWord[] $testWords
 */
class TestWordList extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_word_list';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
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
            'name' => 'Заголовок',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTestWords()
    {
        return $this->hasMany(TestWord::class, ['word_list_id' => 'id']);
    }

    public function getTestWordsCount()
    {
        return $this->getTestWords()->count();
    }

    public function getTestWordsAsArray($filter = null)
    {
        $query = $this->getTestWords();
        if ($filter !== null) {
            $ids = array_map(static function($item) {
                return $item['entity_id'];
            }, $filter);
            $query->andFilterWhere(['not in', 'id', $ids]);
        }
        return $query->asArray()->all();
    }

    public static function getWordListAsArray()
    {
        return ArrayHelper::map(self::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public static function create(string $name)
    {
        $model = new self();
        $model->name = $name;
        return $model;
    }

}
