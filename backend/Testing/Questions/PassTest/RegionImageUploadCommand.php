<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest;

use yii\web\UploadedFile;

class RegionImageUploadCommand
{
    private $imageId;
    private $folderPart;
    private $image;
    private $fragmentId;
    private $testingId;

    public function __construct(string $imageId, string $folderPart, UploadedFile $image, string $fragmentId, int $testingId)
    {
        $this->imageId = $imageId;
        $this->folderPart = $folderPart;
        $this->image = $image;
        $this->fragmentId = $fragmentId;
        $this->testingId = $testingId;
    }

    public function getFolderPart(): string
    {
        return $this->folderPart;
    }

    public function getImage(): UploadedFile
    {
        return $this->image;
    }

    public function getFragmentId(): string
    {
        return $this->fragmentId;
    }

    public function getTestingId(): int
    {
        return $this->testingId;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }
}
