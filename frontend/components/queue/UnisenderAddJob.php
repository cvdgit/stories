<?php


namespace frontend\components\queue;


use common\models\User;
use matperez\yii2unisender\Subscriber;
use matperez\yii2unisender\UniSender;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class UnisenderAddJob extends BaseObject implements JobInterface
{

    public $userID;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        $user = User::findModel($this->userID);

        /** @var UniSender $unisender */
        $unisender = Yii::$app->unisender;

        $sub  = new Subscriber($user->getProfileName(), $user->email);
        $response = $unisender->subscribe($sub, [18159709]);
        if ($response->isSuccess()) {
            // $personId = $response->getResult()['person_id'];
        }
    }
}