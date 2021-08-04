<?php

namespace console\services;

use common\components\unisender\UniSender;
use common\components\unisender\UniSenderCampaign;
use common\components\unisender\UniSenderEmail;
use Yii;
use yii\web\View;

class PublicationService
{

    /** @var object|View */
    private $view;

    /** @var UniSender */
    private $sender;

    public function __construct(UniSender $sender)
    {
        $this->view = Yii::createObject(View::class);
        $this->sender = $sender;
    }

    private function createBody(array $stories)
    {
        return $this->view->render('@common/mail/newStories', ['stories' => $stories]);
    }

    public function sendEmail(array $stories)
    {
        if (empty($stories)) {
            return;
        }
        $response = $this->sender->createEmailMessage(new UniSenderEmail(
            'Wikids',
            'info@wikids.ru',
            'Новые истории на Wikids',
            $this->createBody($stories),
            Yii::$app->params['unisender.listID']
        ));
        if (!$response->isSuccess()) {
            throw new \DomainException($response->getError()->getMessage());
        }
        $result = $response->getResult();
        $response = $this->sender->createCampaign(new UniSenderCampaign(
            $result['message_id'],
            1,
            1
        ));
        if (!$response->isSuccess()) {
            throw new \DomainException($response->getError()->getMessage());
        }
    }
}
