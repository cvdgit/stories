<?php

namespace frontend\widgets;

use common\models\Category;
use common\models\Story;
use yii\base\Widget;

class StoriesTabWidget extends Widget
{

    public $categories;

    private $prefix = 'cat-';

    public function run(): string
    {
        $categories = Category::find()
            //->innerJoinWith('storiesWidget')
            ->andFilterWhere(['in', 'category.alias', $this->categories])
            ->all();

        $categories = array_filter($categories, static function(Category $category) {
            return count($category->storiesWidget) > 0;
        });
        $categories = array_values($categories);

        $stories = [];
        array_map(static function(Category $category) use (&$stories) {
            $stories[$category->alias] = $category->storiesWidget;
        }, $categories);

        array_unshift($categories, Category::create('Популярные', 'popular'));
        $stories['popular'] = Story::findPopularStories();

        return $this->render('stories_tab', [
            'categories' => $categories,
            'stories' => $stories,
            'prefix' => $this->prefix,
        ]);
    }
}