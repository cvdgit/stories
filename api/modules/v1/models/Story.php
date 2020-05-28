<?php


namespace api\modules\v1\models;

use common\helpers\Url;
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
        return $this->hasMany(StorySlide::class, ['story_id' => 'id'])->orderBy(['number' => SORT_ASC]);
    }

    public function extraFields()
    {
        return ['slides'];
    }

}