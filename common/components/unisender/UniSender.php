<?php

namespace common\components\unisender;

use matperez\yii2unisender\Response;
use omgdef\unisender\UniSenderWrapper;

class UniSender
{

    /** @var UniSenderComponent */
    private $uniSender;

    public function __construct()
    {
        $this->uniSender = \Yii::$app->unisender;
    }

    private function getApi(): UniSenderWrapper
    {
        return $this->uniSender->getApi();
    }

    public function createEmailMessage(UniSenderEmail $email): Response
    {
        return new Response($this->getApi()->createEmailMessage([
            'sender_name' => $email->getSenderName(),
            'sender_email' => $email->getSenderEmail(),
            'subject' => $email->getSubject(),
            'body' => $email->getBody(),
            'list_id' => $email->getListID(),
        ]));
    }

    public function createCampaign(UniSenderCampaign $campaign): Response
    {
        return new Response($this->getApi()->createCampaign([
            'message_id' => $campaign->getMessageID(),
            'track_read' => $campaign->getTrackRead(),
            'track_links' => $campaign->getTrackLinks(),
        ]));
    }
}