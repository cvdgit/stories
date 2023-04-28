<?php

declare(strict_types=1);

namespace common\services;

class File
{
    private $folder;
    private $fileName;
    private $extension;
    private $originalFileName;
    private $size;

    public function __construct(string $folder, string $fileName, string $extension, string $originalFileName, int $size)
    {
        $this->folder = $folder;
        $this->fileName = $fileName;
        $this->extension = $extension;
        $this->originalFileName = $originalFileName;
        $this->size = $size;
    }

    public function getFilePath(): string
    {
        return $this->folder . '/' . $this->getFileName();
    }

    public function getFileName(): string
    {
        return $this->fileName . '.' . $this->extension;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getBaseFolder(): string
    {
        return basename($this->folder);
    }
}
