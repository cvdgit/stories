<?php

declare(strict_types=1);

namespace frontend\Game\Deploy;

use DomainException;
use yii\base\Exception;
use yii\helpers\FileHelper;

class ArchUploadHandler
{
    /**
     * @throws Exception
     */
    public function handle(ArchUploadCommand $command): void
    {
        FileHelper::createDirectory($command->getFolder());
        $filePath = $command->getFolder() . '/' . $command->getFileName();
        if (!$command->getUploadedFile()->saveAs($filePath)) {
            throw new DomainException('Ошибка при загрузке архива');
        }
    }
}
