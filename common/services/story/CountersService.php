<?php


namespace common\services\story;


use common\models\Story;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\db\Query;

class CountersService
{

    public function needUpdateCounters()
    {
        if (Yii::$app->user->isGuest) {
            return true;
        }
        $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        $role = array_shift($role);
        return $role->name === UserRoles::ROLE_USER || $role->name === UserRoles::ROLE_ADMIN;
    }

    public function calculateStoryHistoryPercentage(int $userID, int $storyID, int $slideNumber)
    {
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
        $numberOfSlides--; // отнять последнй слайд - Конец
        if ($viewedSlidesNumber > 0 && $numberOfSlides > 0) {
            $percent = $viewedSlidesNumber * 100 / $numberOfSlides;
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
        $updateCounters = true;
        if (Yii::$app->user->isGuest) {
            $command = Yii::$app->db->createCommand();
            $command->insert('{{%story_readonly_statistics}}', [
                'story_id' => $story->id,
                'created_at' => time(),
            ]);
            $command->execute();
        }
        else {
            $this->updateUserStoryHistory(Yii::$app->user->id, $story->id);
            $updateCounters = $this->needUpdateCounters();
        }
        if ($updateCounters) {
            $story->updateCounters(['views_number' => 1]);
        }
    }

}