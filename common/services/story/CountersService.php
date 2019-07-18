<?php


namespace common\services\story;


use common\models\Story;
use common\rbac\UserRoles;
use Yii;
use yii\db\Query;

class CountersService
{

    public function updateUserStoryHistory(int $userID, int $storyID)
    {
        $exists = (new Query())->from('{{%user_story_history}}')->where('user_id = :user AND story_id = :story', ['user' => $userID, 'story' => $storyID])->exists();
        $command = Yii::$app->db->createCommand();
        if ($exists) {
            $command->update('{{%user_story_history}}', ['updated_at' => time()], ['user_id' => $userID, 'story_id' => $storyID]);
        }
        else {
            $command->insert('{{%user_story_history}}', [
                'user_id' => $userID,
                'story_id' => $storyID,
                'updated_at' => time(),
            ]);
        }
        $command->execute();
    }

    public function updateCounters(Story $story): void
    {
        if (!Yii::$app->user->isGuest) {
/*            $role = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            $role = array_shift($role);
            if ($role->name === UserRoles::ROLE_USER) {*/
                $this->updateUserStoryHistory(Yii::$app->user->id, $story->id);
/*            }*/
        }
        $story->updateCounters(['views_number' => 1]);
    }

}