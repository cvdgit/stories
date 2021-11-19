<?php

namespace common\models;

use common\helpers\Translit;
use common\models\story\StoryStatus;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Url;

/**
 * This is the model class for table "site_section".
 *
 * @property int $id
 * @property string $alias
 * @property int $category_id
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $h1
 * @property int $visible
 *
 * @property Category $category
 */
class SiteSection extends ActiveRecord
{

    public static function tableName()
    {
        return 'site_section';
    }

    public function rules()
    {
        return [
            [['category_id', 'title', 'h1'], 'required'],
            [['category_id', 'visible'], 'integer'],
            [['alias', 'title', 'description', 'keywords', 'h1'], 'string', 'max' => 255],
            [['alias'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            ['visible', 'in', 'range' => [0, 1]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => 'Alias',
            'category_id' => 'Дерево категорий',
            'title' => 'Заголовок',
            'description' => 'Description',
            'keywords' => 'Keywords',
            'h1' => 'H1',
            'visible' => 'Показывать',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (empty($this->alias)) {
                $this->alias = Translit::translit($this->title);
            }
            return true;
        }
        return false;
    }

    public function getSectionCategories(): array
    {
        $categories = $this->category->children()->all();
        return array_merge(array_map(static function(Category $category) {
            return $category->id;
        }, $categories), [$this->category->id]);
    }

    public function getCategoriesForMenu(): array
    {
        $root = $this->category;
        if ($root === null) {
            return [];
        }

        $rootItem = ['label' => 'Все категории', 'url' => ['/story/index', 'section' => $this->alias]];

        $storyNumbers = (new Query())
            ->select('COUNT({{%story_category}}.story_id) AS stories_in_category, {{%story_category}}.category_id')
            ->from('{{%category}}')
            ->leftJoin('{{%story_category}}', '{{%story_category}}.category_id = {{%category}}.id')
            ->leftJoin('{{%story}}', '{{%story_category}}.story_id = {{%story}}.id')
            ->where('{{%category}}.tree = :tree', [':tree' => $root->tree])
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
                $item['url'] = ['story/category', 'section' => $this->alias, 'category' => $node->alias];
            }
            return $item;
        });
        array_unshift($items, $rootItem);
        return $items;
    }

    public static function allAsArray(): array
    {
        return self::find()->all();
    }

    public static function allVisibleForMenu(string $sectionAlias = null): array
    {
        return array_map(static function(SiteSection $section) use ($sectionAlias) {
            return [
                'label' => $section->title,
                'url' => $section->getStoriesUrl(),
                'active' => ($section->alias === $sectionAlias),
            ];
        }, self::find()->where('visible = 1')->all());
    }

    public function getStoriesUrl(): string
    {
        return Url::to(['/story/index', 'section' => $this->alias]);
    }

    public static function findByCategory(int $categoryID): self
    {
        return self::find()->where('category_id = :category', [':category' => $categoryID])->one();
    }

    public static function isStories(self $section = null): bool
    {
        if ($section === null) {
            return false;
        }
        return ($section->alias === 'stories');
    }

    public function isVisible(): bool
    {
        return $this->visible === 1;
    }

    public function isOurCategory(Category $category): bool
    {
        $rootCategory = $this->category;
        return $rootCategory->tree === $category->tree;
    }
}
