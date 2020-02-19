<?php


namespace common\helpers;


use matperez\yii2unisender\Response;
use matperez\yii2unisender\UniSender;
use Yii;
use yii\web\View;

class EmailHelper
{

    public static function sendEmail(string $email, string $subject, string $htmlViewName, $viewParams = [])
    {

        /** @var UniSender $unisender */
        $unisender = Yii::$app->unisender;
        $api = $unisender->getApi();

        $result = $api->sendEmail([
            'email' => $email,
            'sender_name' => Yii::$app->name,
            'sender_email' => Yii::$app->params['infoEmail'],
            'subject' => $subject,
            'body' => self::renderEmailView($htmlViewName, $viewParams),
            'list_id' => 17841361
        ]);
        return new Response($result);
    }

    public static function renderEmailView($viewFileName, $params = [])
    {
        $view = Yii::createObject(View::class);
        return $view->render("@common/mail/$viewFileName", $params);
    }

}