<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageUpload;

use yii\base\Exception;
use yii\helpers\FileHelper;
use yii\imagine\Image;

class ImageUploadHandler
{
    /**
     * @throws Exception
     */
    public function handle(ImageUploadCommand $command): void
    {
        FileHelper::createDirectory($command->getFolder());

        $imagePath = $command->getFolder() . $command->getFileName();
        $command->getUploadedFile()->saveAs($imagePath);

        $thumbImagePath = $command->getFolder() . 'thumb_' . $command->getFileName();
        Image::resize($imagePath, 330, 500)->save($thumbImagePath, ['quality' => 100]);

        if (!empty($command->getOldImageFileName())) {
            $oldImages = [
                $command->getFolder() . $command->getOldImageFileName(),
                $command->getFolder() . 'thumb_' . $command->getOldImageFileName(),
            ];
            foreach ($oldImages as $oldImagePath) {
                if (file_exists($oldImagePath)) {
                    FileHelper::unlink($oldImagePath);
                }
            }
        }
    }
}
