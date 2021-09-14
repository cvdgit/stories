<?php

namespace common\services\story;

use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\db\Query;

class CountersService
{

    public function needUpdateCounters(): bool
    {
        return true;
/*        if (Yii::$app->user->isGuest) {
            return true;
        }
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        $role = array_shift($role);
        return in_array($role->name, [UserRoles::ROLE_USER, UserRoles::ROLE_STUDENT, UserRoles::ROLE_ADMIN], true);*/
    }

    public function calculateStoryHistoryPercentage(int $userID, int $storyID): void
    {
        $storyModel = Story::findOne($storyID);
        if ($storyModel === null) {
            return;
        }
        $viewedSlidesNumber = (new Query())
            ->select('slide_id')
            ->from('{{%story_statistics}}')
            ->where('story_id = :story', [':story' => $storyID])
            ->andWhere('user_id = :user', [':user' => $userID])
            ->groupBy(['slide_id'])
            ->count();
        // все видимые слайды
        $numberOfSlides = (new Query())
            ->select('id')
            ->from('{{%story_slide}}')
            ->where('story_id = :story', [':story' => $storyID])
            ->andWhere('status = :status', [':status' => StorySlide::STATUS_VISIBLE])
            ->count();
        $numberOfSlides--; // отнять последний слайд - Конец
        if ($viewedSlidesNumber > 0 && $numberOfSlides > 0) {
            $percent = round($viewedSlidesNumber * 100 / $numberOfSlides);
            if ($percent > 100) {
                $percent = 100;
            }
            $command = Yii::$app->db->createCommand();
            $command->update('{{%user_story_history}}', ['percent' => $percent], ['user_id' => $userID, 'story_id' => $storyID]);
            $command->execute();
        }
    }

    public function updateUserStoryHistory(int $userID, int $storyID)
    {
        $exists = (new Query())->from('{{%user_story_history}}')->where('user_id = :user AND story_id = :story', ['user' => $userID, 'story' => $storyID])->exists();
        $command = Yii::$app->db->createCommand();
        if ($exists) {
            $command->update('{{%user_story_history}}', ['updated_at' => time()], ['user_id' => $userID, 'story_id' => $storyID]);
        }
        else {
            $command->insert('{{%user_story_history}}', ['user_id' => $userID, 'story_id' => $storyID, 'updated_at' => time()]);
        }
        $command->execute();
    }

    public function updateCounters(Story $story): void
    {
        if (!Yii::$app->crawlerdetect->isCrawler()) {
            $updateCounters = true;
            if (Yii::$app->user->isGuest) {
                $command = Yii::$app->db->createCommand();
                $command->insert('{{%story_readonly_statistics}}', [
                    'story_id' => $story->id,
                    'created_at' => time(),
                ]);
                $command->execute();
            } else {
                $this->updateUserStoryHistory(Yii::$app->user->id, $story->id);
                $updateCounters = $this->needUpdateCounters();
            }
            if ($updateCounters) {
                $story->updateCounters(['views_number' => 1]);
            }
        }
    }
}
