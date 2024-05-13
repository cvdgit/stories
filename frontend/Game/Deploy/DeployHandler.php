<?php

declare(strict_types=1);

namespace frontend\Game\Deploy;

use yii\base\Exception;
use yii\helpers\FileHelper;
use ZipArchive;

class DeployHandler
{
    /**
     * @throws Exception
     */
    public function handle(DeployCommand $command): void
    {
        $zip = new ZipArchive();
        if ($zip->open($command->getArchFilePath()) === false) {
            $zip->close();
            throw new \DomainException('Невозможно открыть архив');
        }

        FileHelper::createDirectory($command->getFolder());

        for ($i = 0; $i < $zip->count(); $i++) {
            $filename = $zip->getNameIndex($i);

            if (strpos($filename, '__MACOSX') !== false) {
                continue;
            }

            if (pathinfo($filename, PATHINFO_EXTENSION) === 'php') {
                continue;
            }

            if (strpos($filename, $command->getBuildName() . '/Build/') !== false) {
                $zip->extractTo($command->getFolder(), $filename);
            }

            if (strpos($filename, $command->getBuildName() . '/StreamingAssets/') !== false) {
                $zip->extractTo($command->getFolder(), $filename);
            }

            if (strpos($filename, $command->getBuildName() . '/TemplateData/') !== false) {
                $zip->extractTo($command->getFolder(), $filename);
            }
        }
        $zip->close();
    }
}
