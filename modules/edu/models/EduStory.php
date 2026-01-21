<?php

declare(strict_types=1);

namespace modules\edu\models;

use common\models\StoryStudentProgress;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @property int $id [int(11)]
 * @property string $title [varchar(255)]
 * @property string $alias [varchar(255)]
 * @property string $body
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property int $user_id [int(11)]
 * @property string $cover [varchar(255)]
 * @property int $status [smallint(6)]
 * @property int $category_id [int(11)]
 * @property int $sub_access [smallint(6)]
 * @property string $story_file [varchar(255)]
 * @property string $description [varchar(1024)]
 * @property int $source_id [smallint(6)]
 * @property int $views_number [int(11)]
 * @property int $slides_number [int(11)]
 * @property int $audio [tinyint(3)]
 * @property int $user_audio [tinyint(3)]
 * @property int $episode [int(11)]
 * @property int $video [tinyint(3)]
 * @property int $published_at [int(11)]
 * @property int $have_neo_relation [tinyint(3)]
 * @property int $access_by_link [tinyint(3)]
 *
 * @property EduStorySlide[] $storySlides
 */
class EduStory extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%story}}';
    }

    public function slidesData(bool $withoutQuestions = false): string
    {
        $slides = (new Query())->from('{{%story_slide}} AS t1')
            ->select(['t1.data', 't2.data AS link_data', 't1.id'])
            ->leftJoin('{{%story_slide}} t2', 't2.id = t1.link_slide_id')
            ->where('t1.`story_id` = :story', [':story' => $this->id])
            ->andWhere('t1.`status` = :status', [':status' => 1])
            ->orderBy(['t1.number' => SORT_ASC]);
        if ($withoutQuestions) {
            $slides->andWhere('t1.`kind` <> 2');
        }
        $slides = $slides->all();
        $data = '';
        foreach ($slides as $slide) {
            $slideData = $slide['link_data'] ?? $slide['data'];
            if ($slideData === 'link') {
                continue;
            }
            $data .= $slideData;
            $search = [
                'data-id=""',
                'data-id="0"',
                'data-background-color="#000000"',
            ];
            $replace = [
                'data-id="' . $slide['id'] . '"',
                'data-id="' . $slide['id'] . '"',
                'data-background-color="#fff"',
            ];
            $data = str_replace($search, $replace, $data);
        }

        $data .= '<section data-id="final-slide" data-background-color="#fff"></section>';

        return '<div class="slides">' . $data . '</div>';
    }

    public function slideBlocksData(): array
    {
        return (new Query())
            ->select('t1.id AS slideID, t2.title, t2.href')
            ->from('{{%story_slide}} AS t1')
            ->innerJoin('{{%story_slide_block}} t2', 't1.id = t2.slide_id')
            ->where('t1.story_id = :story', [':story' => $this->id])
            ->andWhere('t1.status = :status', [':status' => 1])
            ->andWhere(['in', 't2.type', [1, 2]])
            ->all();
    }

    public function isComplete(int $progress): bool
    {
        return $progress === 100;
    }

    public function getStorySlides(): ActiveQuery
    {
        return $this->hasMany(EduStorySlide::class, ['story_id' => 'id'])
            ->orderBy(['number' => SORT_ASC]);
    }
}
