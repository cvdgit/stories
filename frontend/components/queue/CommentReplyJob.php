<?php

namespace frontend\components\queue;

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
        $sent = Yii::$app->mailer
            ->compose(['html' => 'commentReply-html', 'text' => 'commentReply-text'], [
                'story' => $story,
                'commentAuthor' => $commentAuthor,
                'replyUser' => $replyUser,
                ])
            ->setTo($commentAuthor->email)
            ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
            ->setSubject('Ответ на ваш комментарий')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Reply email sent error');
        }
    }
}