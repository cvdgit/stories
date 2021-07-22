<?php

namespace backend\models;

use common\models\StoryTest;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "related_tests".
 *
 * @property int $test_id
 * @property int $related_test_id
 * @property int $order;
 *
 * @property StoryTest $relatedTest
 * @property StoryTest $test
 */
class RelatedTests extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'related_tests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_id', 'related_test_id'], 'required'],
            [['test_id', 'related_test_id', 'order'], 'integer'],
            [['test_id', 'related_test_id'], 'unique', 'targetAttribute' => ['test_id', 'related_test_id']],
            [['related_test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['related_test_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'test_id' => 'Test ID',
            'related_test_id' => 'Related Test ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelatedTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'related_test_id'])->orderBy(['order' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(StoryTest::class, ['id' => 'test_id']);
    }

    public static function create(int $testID, int $relatedTestID, int $order = 1): self
    {
        $model = new self();
        $model->test_id = $testID;
        $model->related_test_id = $relatedTestID;
        $model->order = $order;
        return $model;
    }

    public static function deleteByTestID(int $testID): void
    {
        self::deleteAll('test_id = :test', [':test' => $testID]);
    }
}
