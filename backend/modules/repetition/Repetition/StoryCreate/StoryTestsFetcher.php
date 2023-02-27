<?php

declare(strict_types=1);

namespace backend\modules\repetition\Repetition\StoryCreate;

use yii\db\Query;

class StoryTestsFetcher
{
    public function fetch(int $storyId): array
    {
        return (new Query())
            ->select([
                'testId' => 'st.id',
                'testName' => 'st.header',
            ])
            ->from(['tl' => 'story_story_test'])
            ->innerJoin(['st' => 'story_test'], 'tl.test_id = st.id')
            ->where(['tl.story_id' => $storyId])
            ->orderBy(['testName' => SORT_ASC])
            ->all();
    }
}
