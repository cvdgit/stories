<?php


namespace common\services;


use frontend\models\StoryLikeForm;
use Yii;
use yii\db\Query;

class StoryLikeService
{

    public function getLikeCount(int $storyID)
    {
        return (new Query())->from('{{%story_like}}')->where('story_id = :id AND action = :action', [':id' => $storyID, ':action' => StoryLikeForm::LIKE])->count();
    }

    public function getDislikeCount(int $storyID)
    {
        return (new Query())->from('{{%story_like}}')->where('story_id = :id AND action = :action', [':id' => $storyID, ':action' => StoryLikeForm::DISLIKE])->count();
    }

    public function getUserStoryAction(int $storyID, int $userID)
    {
        return (new Query())->select('action')->from('{{%story_like}}')->where('story_id = :story AND user_id = :user', [':story' => $storyID, ':user' => $userID])->scalar();
    }

    public function userStoryActionExists(int $storyID, int $userID): bool
    {
        return (new Query())->from('{{%story_like}}')->where('story_id = :story AND user_id = :user', [':story' => $storyID, ':user' => $userID])->exists();
    }

    public function rate(int $storyID, int $userID, int $action): int
    {
        $command = Yii::$app->db->createCommand();
        $exists = $this->userStoryActionExists($storyID, $userID);
        if ($exists) {
            $command->update('{{%story_like}}', ['action' => $action], 'story_id = :story AND user_id = :user', [':story' => $storyID, ':user' => $userID]);
        }
        else {
            $command->insert('{{%story_like}}', ['story_id' => $storyID, 'user_id' => $userID, 'action' => $action]);
        }
        return $command->execute();
    }

}