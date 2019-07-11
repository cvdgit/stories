<?php


namespace common\services;


use Yii;
use yii\db\Query;

class StoryFavoritesService
{

    public function storyInFavorites(int $userID, int $storyID)
    {
        return (new Query())->from('{{%story_favorites}}')->where('story_id = :story AND user_id = :user', [':story' => $storyID, ':user' => $userID])->exists();
    }

    public function add(int $userID, int $storyID)
    {
        $exists = $this->storyInFavorites($userID, $storyID);
        $command = Yii::$app->db->createCommand();
        if ($exists) {
            $command->delete('{{%story_favorites}}', 'story_id = :story AND user_id = :user', [':story' => $storyID, ':user' => $userID]);
        }
        else {
            $command->insert('{{%story_favorites}}', ['user_id' => $userID, 'story_id' => $storyID]);
        }
        return $command->execute();
    }

}