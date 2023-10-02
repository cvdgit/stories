<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Create;

use DomainException;
use yii\base\Exception;
use yii\helpers\FileHelper;

class UploadImageGapsHandler
{
    /**
     * @throws Exception
     */
    public function handle(UploadImageGapsCommand $command): void
    {
        FileHelper::createDirectory($command->getFolder());
        $filePath = $command->getFolder() . '/' . $command->getFileId() . '.' . $command->getFile()->extension;
        if (file_exists($filePath)) {
            FileHelper::unlink($filePath);
        }
        if (!$command->getFile()->saveAs($filePath)) {
            throw new DomainException('File save error');
        }
    }
}
