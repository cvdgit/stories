<?php


namespace console\controllers;


use common\models\User;
use matperez\yii2unisender\Subscriber;
use matperez\yii2unisender\UniSender;
use Yii;
use yii\console\Controller;

class UnisenderController extends Controller
{

    public function actionSubscribers()
    {
        $models = User::find()->where('status = :active', [':active' => User::STATUS_ACTIVE])->all();
        /** @var UniSender $unisender */
        $unisender = Yii::$app->unisender;
        foreach ($models as $user) {
            $sub  = new Subscriber($user->getProfileName(), $user->email);
            $response = $unisender->subscribe($sub, [Yii::$app->params['unisender.listID']]);
            if ($response->isSuccess()) {
                // $personId = $response->getResult()['person_id'];
                echo 'OK - ' . $user->email . PHP_EOL;
            }
        }
    }

}