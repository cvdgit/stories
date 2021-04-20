<?php

namespace common\components\unisender;

class UniSenderEmail
{

    /** @var string */
    private $senderName;

    /** @var string */
    private $senderEmail;

    /** @var string */
    private $subject;

    /** @var string */
    private $body;

    /** @var int */
    private $listID;

    public function __construct(string $senderName, string $senderEmail, string $subject, string $body, int $listID)
    {
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->subject = $subject;
        $this->body = $body;
        $this->listID = $listID;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getListID(): int
    {
        return $this->listID;
    }

}