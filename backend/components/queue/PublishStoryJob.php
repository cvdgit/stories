<?php


namespace backend\components\queue;


use common\models\Story;
use matperez\yii2unisender\UniSender;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;
use yii\web\View;

class PublishStoryJob extends BaseObject implements JobInterface
{

    /** @var int */
    public $storyID;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     * @throws yii\base\InvalidConfigException
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
            'subject' => 'Новая история на Wikids',
            'body' => $view->render('@common/mail/newStory-html', ['story' => $story]),
            'list_id' => 17841361,
        ]);
        $messageID = $result['result']['message_id'];

        $api->createCampaign([
            'message_id' => $messageID,
        ]);
    }
}