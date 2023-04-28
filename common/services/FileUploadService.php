<?php

declare(strict_types=1);

namespace common\services;

use http\Exception\RuntimeException;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class FileUploadService
{
    private $rootPath;

    public function __construct()
    {
        $this->rootPath = Yii::getAlias('@public');
    }

    /**
     * @throws Exception
     */
    public function uploadFile(string $folderPart, UploadedFile $file): File
    {
        $folder = $this->rootPath . $folderPart;
        FileHelper::createDirectory($folder);

        FileHelper::createDirectory($folder);

        $extension = $file->extension;

        $fileName = Uuid::uuid4()->toString();

        $filePath = $folder . '/' . $fileName . '.' . $extension;
        if (file_exists($filePath)) {
            FileHelper::unlink($filePath);
        }

        if (!$file->saveAs($filePath)) {
            throw new RuntimeException('Ошибка при загрузке файла со схемой');
        }

        return new File($folder, $fileName, $extension, $fileName . '.' . $extension, $file->size);
    }

    public function deleteFile(string $folder, string $fileName, string $extension): void
    {
        $folder = $this->rootPath . $folder;
        $path = $folder . $fileName . '.' . $extension;
        if (file_exists($path)) {
            FileHelper::unlink($path);
        }
    }
}
