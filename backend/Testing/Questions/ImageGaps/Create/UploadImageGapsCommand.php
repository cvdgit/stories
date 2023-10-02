<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Create;

use yii\web\UploadedFile;

class UploadImageGapsCommand
{
    /**
     * @var string
     */
    private $fileId;
    /**
     * @var string
     */
    private $folder;
    /**
     * @var UploadedFile
     */
    private $file;

    public function __construct(string $fileId, string $folder, UploadedFile $file)
    {
        $this->fileId = $fileId;
        $this->folder = $folder;
        $this->file = $file;
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }
}
