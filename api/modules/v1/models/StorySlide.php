<?php

namespace api\modules\v1\models;

use backend\components\SlideModifier;
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
            'data' => function() {
                $slideData = $this->data;
                $search = [
                    'data-id=""',
                    'data-background-color="#000000"',
                ];
                $replace = [
                    'data-id="' . $this->id . '"',
                    'data-background-color="#fff"',
                ];
                $slideData = str_replace($search, $replace, $slideData);
                return (new SlideModifier($this->id, $slideData))
                    ->addImageUrl()
                    ->addVideoUrl()
                    ->render();
            },
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