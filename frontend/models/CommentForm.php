<?php


namespace frontend\models;


use common\models\Comment;
use common\models\User;
use Yii;
use yii\base\Model;
use common\models\Story;

class CommentForm extends Model
{

    public $body;
    public $story_id;

    public function __construct(int $storyID, $config = [])
    {
        parent::__construct($config);
        $this->story_id = $storyID;
    }

    public function rules()
    {
        return [
            [['story_id', 'body'], 'required'],
            [['story_id'], 'integer'],
            [['story_id'], 'exist', 'targetClass' => Story::class, 'targetAttribute' => ['story_id' => 'id']],
        ];
    }

    public function createComment($userId)
    {
        $comment = new Comment();
        $comment->story_id = $this->story_id;
        $comment->user_id = $userId;
        $comment->body = $this->body;
        return $comment->save();
    }

    public function getCurrentUserProfilePhotoPath()
    {
        $noAvatar = '/img/avatar.png';
        if (!Yii::$app->user->isGuest) {
            $user = User::findModel(Yii::$app->user->id);
            if ($user->profile !== null) {
                $profilePhoto = $user->profile->profilePhoto;
                if ($profilePhoto !== null) {
                    return $profilePhoto->getThumbFileUrl('file', 'list', $noAvatar);
                }
            }
        }
        return $noAvatar;
    }

}