<?php

declare(strict_types=1);

namespace frontend\Game\Deploy;

use yii\web\UploadedFile;

class ArchUploadCommand
{
    /**
     * @var string
     */
    private $folder;
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var UploadedFile
     */
    private $uploadedFile;

    public function __construct(string $folder, string $fileName, UploadedFile $uploadedFile)
    {
        $this->folder = $folder;
        $this->fileName = $fileName;
        $this->uploadedFile = $uploadedFile;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }
}
