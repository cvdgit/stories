<?php


namespace backend\components\queue;


use common\models\Story;
use matperez\yii2unisender\UniSender;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;
use yii\web\View;

class PublishAudioJob extends BaseObject implements JobInterface
{

    /** @var int */
    public $storyID;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        $story = Story::findModel($this->storyID);

        /** @var UniSender $unisender */
        $unisender = Yii::$app->unisender;
        $api = $unisender->getApi();

        $view = Yii::createObject(View::class);
        $result = $api->createEmailMessage([
            'sender_name' => 'Wikids',
            'sender_email' => 'info@wikids.ru',
            'subject' => 'Новая озвучка для истории ' . $story->title . ' на Wikids',
            'body' => $view->render('@common/mail/newAudio-html', ['story' => $story]),
            'list_id' => Yii::$app->params['unisender.listID'],
        ]);
        $messageID = $result['result']['message_id'];

        $api->createCampaign([
            'message_id' => $messageID,
            'track_read' => 1,
            'track_links' => 1,
        ]);
    }
}