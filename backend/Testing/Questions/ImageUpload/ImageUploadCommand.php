<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageUpload;

use yii\web\UploadedFile;

class ImageUploadCommand
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
    /**
     * @var string|null
     */
    private $oldImageFileName;

    public function __construct(string $folder, string $fileName, UploadedFile $uploadedFile, string $oldImageFileName = null)
    {
        $this->folder = $folder;
        $this->fileName = $fileName;
        $this->uploadedFile = $uploadedFile;
        $this->oldImageFileName = $oldImageFileName;
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

    public function getOldImageFileName(): ?string
    {
        return $this->oldImageFileName;
    }
}
