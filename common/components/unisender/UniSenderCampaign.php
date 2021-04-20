<?php

namespace common\components\unisender;

class UniSenderCampaign
{

    /** @var int */
    private $messageID;

    /** @var int */
    private $trackRead;

    /** @var int */
    private $trackLinks;

    public function __construct(int $messageID, int $trackRead, int $trackLinks)
    {
        $this->messageID = $messageID;
        $this->trackRead = $trackRead;
        $this->trackLinks = $trackLinks;
    }

    /**
     * @return int
     */
    public function getMessageID(): int
    {
        return $this->messageID;
    }

    /**
     * @return int
     */
    public function getTrackRead(): int
    {
        return $this->trackRead;
    }

    /**
     * @return int
     */
    public function getTrackLinks(): int
    {
        return $this->trackLinks;
    }
}
