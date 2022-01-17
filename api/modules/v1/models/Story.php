<?php


namespace api\modules\v1\models;

use common\helpers\Url;
use common\models\Category;
use yii\db\ActiveRecord;

class Story extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%story}}';
    }

    public function fields()
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

    public function getSlides()
    {
        return $this
            ->hasMany(StorySlide::class, ['story_id' => 'id'])
            ->andWhere(['kind' => \common\models\StorySlide::KIND_SLIDE])
            ->orderBy(['number' => SORT_ASC]);
    }

    public function extraFields()
    {
        return ['slides'];
    }

    public function getCategories(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('story_category', ['story_id' => 'id']);
    }
}