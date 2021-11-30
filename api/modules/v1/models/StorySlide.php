<?php

namespace api\modules\v1\models;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use common\helpers\Url;
use yii\db\ActiveRecord;

class StorySlide extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%story_slide}}';
    }

    public function fields()
    {
        return [
            'id',
            'number',
            'data',
            'images' => function() {
                $images = [];
                if ($this->data === null) {
                    return $images;
                }
                $reader = new HtmlSlideReader($this->data);
                $slide = $reader->load();
                foreach ($slide->getBlocks() as $block) {
                    if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                        $path = $block->getFilePath();
                        if (strpos($path, 'http') === false) {
                            $path = Url::homeUrl() . $path;
                        }
                        $images[] = $path;
                    }
                }
                return $images;
            },
        ];
    }

    public function extraFields()
    {
        return [];
    }

}