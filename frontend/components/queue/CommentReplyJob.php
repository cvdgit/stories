<?php

namespace frontend\components\queue;

use common\helpers\EmailHelper;
use common\models\Story;
use common\models\User;
use RuntimeException;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class CommentReplyJob extends BaseObject implements JobInterface
{

    /** @var int */
    public $storyID;

    /** @var int */
    public $commentAuthorID;

    /** @var int */
    public $replyUserID;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        $story = Story::findModel($this->storyID);
        $commentAuthor = User::findModel($this->commentAuthorID);
        $replyUser = User::findModel($this->replyUserID);

        $response = EmailHelper::sendEmail($commentAuthor->email, 'Ответ на ваш комментарий на ' . Yii::$app->name, 'commentReply-html', ['story' => $story, 'commentAuthor' => $commentAuthor, 'replyUser' => $replyUser]);
        if (!$response->isSuccess()) {
            throw new RuntimeException('Reply email sent error');
        }
    }
}