<?php

namespace api\modules\v1\models;

use common\helpers\Url;
use common\models\Category;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string cover
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property string $body
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_id
 * @property int $status
 * @property int $category_id
 * @property int $sub_access
 * @property string $story_file
 * @property string $description
 * @property int $source_id
 * @property int $views_number
 * @property int $slides_number
 * @property bool $audio
 * @property bool $user_audio
 * @property int $episode
 * @property bool $video
 * @property int $published_at
 * @property bool $have_neo_relation
 * @property-read ActiveQuery $categories
 * @property-read ActiveQuery $tests
 * @property-read ActiveQuery $slides
 * @property-read ActiveQuery $allSlides
 * @property bool $access_by_link
 */
class Story extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%story}}';
    }

    /**
     * @inheritdoc
     */
    public function fields(): array
    {
        return [
            'id',
            'title',
            'cover' => function() {
                $url = '';
                if ($this->cover !== null) {
                    $url = Url::homeUrl() . '/slides_cover/list/' . $this->cover;
                }
                return $url;
            }
        ];
    }

    public function getSlides(): ActiveQuery
    {
        return $this
            ->hasMany(StorySlide::class, ['story_id' => 'id'])
            ->andWhere(['kind' => \common\models\StorySlide::KIND_SLIDE])
            ->orderBy(['number' => SORT_ASC]);
    }

    public function getAllSlides(): ActiveQuery
    {
        return $this
            ->hasMany(StorySlide::class, ['story_id' => 'id'])
            ->orderBy(['number' => SORT_ASC]);
    }

    public function extraFields(): array
    {
        return ['slides', 'allSlides', 'tests'];
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('story_category', ['story_id' => 'id']);
    }

    public function getTests(): ActiveQuery
    {
        return $this->hasMany(StoryTest::class, ['id' => 'test_id'])
            ->viaTable('story_story_test', ['story_id' => 'id']);
    }
}