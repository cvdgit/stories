<?php

declare(strict_types=1);

namespace backend\MentalMap\FetchMentalMapUserHistory;

use yii\db\Expression;
use yii\db\Query;

class MentalMapUserHistoryFetcher
{
    public function fetch(int $storyId, int $userId): array
    {
        $subQuery = (new Query())
            ->select([
                'userId' => 'h.user_id',
                'mentalMapId' => 'h.mental_map_id',
                'imageFragmentId' => 'h.image_fragment_id',
                'maxHistoryItemId' => new Expression(
                    "SUBSTRING_INDEX(GROUP_CONCAT(h.id ORDER BY h.overall_similarity DESC), ',', 1)",
                ),
            ])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.story_id' => $storyId,
                'h.user_id' => $userId,
            ])
            ->groupBy(['h.mental_map_id', 'h.image_fragment_id']);

        $historyQuery = (new Query())
            ->select([
                'userId' => 'h.user_id',
                'slideId' => 'h.slide_id',
                'mentalMapId' => 'h.mental_map_id',
                'imageFragmentId' => 'h.image_fragment_id',
                'all' => 'h2.overall_similarity',
                'hiding' => 'h2.text_hiding_percentage',
                'target' => 'h2.text_target_percentage',
                'content' => 'h2.content',
                'createdAt' => 'h2.created_at',
            ])
            ->distinct()
            ->from(['h' => 'mental_map_history'])
            ->innerJoin(['t' => $subQuery],
                't.userId = h.user_id AND t.mentalMapId = h.mental_map_id AND t.imageFragmentId = h.image_fragment_id')
            ->innerJoin(['h2' => 'mental_map_history'], 'h2.id = t.maxHistoryItemId')
            ->where([
                'h.story_id' => $storyId,
                'h.user_id' => $userId,
            ])
            ->groupBy(['h.slide_id', 'h.mental_map_id', 'h.image_fragment_id']);

        $query = (new Query())
            ->select([
                't.*',
                'userName' => new Expression(
                    "CASE WHEN p.id IS NULL THEN u.email ELSE CONCAT(p.last_name, ' ', p.first_name) END",
                ),
                'slideNumber' => 's.number',
            ])
            ->from(['t' => $historyQuery])
            ->innerJoin(['s' => 'story_slide'], 't.slideId = s.id')
            ->innerJoin(['u' => 'user'], 't.userId = u.id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id');

        return array_map(static function (array $row): MentalMapUserHistoryItem {
            return new MentalMapUserHistoryItem(
                $row['mentalMapId'],
                $row['imageFragmentId'],
                (int) $row['all'],
                (int) $row['hiding'],
                (int) $row['target'],
                $row['content'],
                (int) $row['createdAt'],
            );
        }, $query->all());
    }
}
