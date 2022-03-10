<?php

namespace api\modules\v1\models;

use backend\components\SlideModifier;
use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use common\helpers\Url;
use common\models\slide\SlideKind;
use yii\db\ActiveRecord;

/**
 * @property int $id [int(11)]
 * @property int $story_id [int(11)]
 * @property int $number [smallint(6)]
 * @property bool $status [tinyint(3)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property bool $kind [tinyint(3)]
 * @property int $link_slide_id [int(11)]
 * @property string $data
 */
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
                if (SlideKind::isLink($this)) {
                    $slideData = StorySlide::findOne($this->link_slide_id)->data;
                }

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

    public static function getSlideData(self $model): string
    {
        if (SlideKind::isLink($model)) {
            if (($slideLinkId = $model->link_slide_id) === null) {
                throw new \DomainException('Slide link ID is null');
            }
            if (($slideLinkModel = self::findOne($slideLinkId)) === null) {
                throw new \DomainException('Linked slide is null');
            }
            return $slideLinkModel->id;
        }
        return $model->data;
    }

    public static function findSlideByNumber(int $storyId, int $number)
    {
        return self::find()
            ->where('story_id = :story', [':story' => $storyId])
            ->andWhere('number = :number', [':number' => $number])
            ->one();
    }
}