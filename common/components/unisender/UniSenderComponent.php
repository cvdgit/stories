<?php

namespace common\components\unisender;

class UniSenderComponent extends \matperez\yii2unisender\UniSender
{

    public $apiConfig = [
        'apiKey' => '',
    ];

    /**
     * @return WikidsUniSenderWrapper
     */
    protected function createApi()
    {
        $defaults = [
            'senderPhone' => '+74997033525',
            'senderName' => 'Wikids',
            'senderEmail' => 'info@wikids.ru',
            'apiKey' => '',
            'encoding' => 'UTF8',
            'timeout' => 10,
            'retryCount' => 0,
        ];
        $config = array_merge($defaults, $this->apiConfig);
        $api = new WikidsUniSenderWrapper();
        foreach ($config as $name => $value) {
            $api->$name = $value;
        }
        return $api;
    }
}