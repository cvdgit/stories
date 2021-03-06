<?php

namespace common\models;

use common\helpers\Translit;
use common\models\story\StoryStatus;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

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
 * @property string $sort_field
 * @property int $sort_order
 *
 * @property StoryCategory[] $storyCategories
 * @property Story[] $stories
 */
class Category extends ActiveRecord
{

    public $parentNode;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'category';
    }

    public function behaviors(): array
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'treeAttribute' => 'tree',
            ],
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['tree', 'lft', 'rgt', 'depth', 'parentNode', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['name', 'alias'], 'string', 'max' => 255],
            [['sort_field'], 'string', 'max' => 50],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
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
            'sort_field' => 'Сортировка',
            'sort_order' => 'Направление сортировки',
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
    public function getStoryCategories()
    {
        return $this->hasMany(StoryCategory::class, ['category_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStories()
    {
        return $this->hasMany(Story::class, ['id' => 'story_id'])->viaTable('story_category', ['category_id' => 'id']);
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (empty($this->alias)) {
                $this->alias = str_replace(' ', '-', strtolower(Translit::translit($this->name)));
            }
            return true;
        }
        return false;
    }

    /**
     * Convert a tree into nested arrays. If you use the default function parameters you get
     * a set compatible with Yii2 Menu widget.
     *
     * @param int $depth
     * @param string $itemsKey
     * @param callable|null $getDataCallback
     * @return array
     */
    public function toNestedArray($depth = null, $itemsKey = 'items', $getDataCallback = null)
    {
        /** @var Category $nodes */
        $nodes = $this->children($depth)->all();
        $exportedAttributes = array_diff(array_keys($this->attributes), ['lft', 'rgt']);
        $trees = [];
        $stack = [];
        foreach ($nodes as $node) {
            if ($getDataCallback) {
                $item = call_user_func($getDataCallback, $node);
            } else {
                $item = $node->toArray($exportedAttributes);
                $item['url'] = ['/story/category', 'category' => $item['alias']];
            }
            $item[$itemsKey] = [];
            $l = count($stack);
            while ($l > 0 && $stack[$l - 1]['depth'] >= $item['depth']) {
                array_pop($stack);
                $l--;
            }
            if ($l === 0) {
                // Assign root node
                $i = count($trees);
                $trees[$i] = $item;
                $stack[] = &$trees[$i];
            } else {
                // Add node to parent
                $i = count($stack[$l - 1][$itemsKey]);
                $stack[$l - 1]['folder'] = true;
                $stack[$l - 1][$itemsKey][$i] = $item;
                $stack[] = &$stack[$l - 1][$itemsKey][$i];
            }
        }
        return $trees;
    }

    public static function getCategoriesForMenu()
    {
        $root = self::findOne(1);
        if ($root === null) {
            return [];
        }
        
        $rootItem = ['label' => 'Все категории', 'url' => ['/story/index']];

        $storyNumbers = (new Query())
            ->select('COUNT({{%story_category}}.story_id) AS stories_in_category, {{%story_category}}.category_id')
            ->from(self::tableName())
            ->leftJoin('{{%story_category}}', '{{%story_category}}.category_id = {{%category}}.id')
            ->leftJoin('{{%story}}', '{{%story_category}}.story_id = {{%story}}.id')
            ->where('tree = :tree', [':tree' => $root->tree])
            ->andWhere('{{%story}}.status = :story_stat', [':story_stat' => StoryStatus::PUBLISHED])
            ->groupBy(['{{%story_category}}.category_id'])
            ->indexBy('category_id')
            ->all();

        $items = $root->toNestedArray(null, 'items', function($node) use ($storyNumbers) {
            $storiesInCategory = 0;
            if (isset($storyNumbers[$node->id])) {
                $storiesInCategory = (int)$storyNumbers[$node->id]['stories_in_category'];
            }
            $item = [
                'label' => $node->name,
                'depth' => $node->depth,
            ];
            if ($storiesInCategory > 0) {
                $item['url'] = ['/story/category', 'category' => $node->alias];
            }
            return $item;
        });
        array_unshift($items, $rootItem);
        return $items;
    }

    public static function categoryArray()
    {
        $root = self::findOne(1);
        if ($root === null) {
            return [];
        }
        $items = $root->toNestedArray(null, 'items', function($node) {
            return [
                'label' => $node->name,
                'url' => $node->id,
                'depth' => $node->depth
            ];
        });
        return $items;
    }

    public static function categoryArray2()
    {
        $root = self::findOne(1);
        $items = $root->toNestedArray(null, 'children', function($node) {
            return [
                'title' => $node->name,
                'url' => $node->id,
                'depth' => $node->depth
            ];
        });
        return $items;
    }

    /**
     * @param $id
     * @return Category
     * @throws NotFoundHttpException
     */
    public static function findModel($id): self
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Категория не найдена');
    }

    /**
     * @param $alias
     * @return Category
     * @throws NotFoundHttpException
     */
    public static function findModelByAlias($alias): self
    {
        if (($model = self::findOne(['alias' => $alias])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Категория не найдена');
    }

    public function subCategories()
    {
        $categories = $this->children()->all();
        return array_merge(array_map(function(Category $category) {
            return $category->id;
        }, $categories), [$this->id]);
    }

    public function getCategoryUrl()
    {
        return Url::to(['story/category', 'category' => $this->alias]);
    }

}
