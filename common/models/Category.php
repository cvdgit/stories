<?php

namespace common\models;

use Yii;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property int $tree
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property string $name
 * @property string $alias
 * @property string $description
 */
class Category extends \yii\db\ActiveRecord
{

    public $parentNode;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'alias'], 'required'],
            [['tree', 'lft', 'rgt', 'depth', 'parentNode'], 'integer'],
            [['description'], 'string'],
            [['name', 'alias'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => 'Tree',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'name' => 'Название',
            'alias' => 'Alias',
            'description' => 'Описание',
            'parentNode' => 'Родительская категория',
        ];
    }

    public static function find()
    {
        return new CategoryQuery(get_called_class());
    }

    public static function getCategoryArray()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::className(), ['id' => 'category_id']);
    }

    /**
     * @return ActiveDataProvider
     */
    public function getPublishedStories()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $this->getStories()->published()
        ]);
        return $dataProvider;
    }

}
