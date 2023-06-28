<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Create;

use yii\web\UploadedFile;

class CreateFileCommand
{
    /**
     * @var string
     */
    private $uuid;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $captions;
    /**
     * @var UploadedFile
     */
    private $videoFile;

    public function __construct(string $uuid, string $name, UploadedFile $videoFile, string $captions = null)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->captions = $captions;
        $this->videoFile = $videoFile;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getCaptions(): ?string
    {
        return $this->captions;
    }

    /**
     * @return UploadedFile
     */
    public function getVideoFile(): UploadedFile
    {
        return $this->videoFile;
    }
}
