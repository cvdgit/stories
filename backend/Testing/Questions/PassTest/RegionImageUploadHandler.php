<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest;

use common\components\ModelDomainException;
use common\models\StorySlideImage;
use common\services\File;
use common\services\FileUploadService;
use Exception;
use yii\imagine\Image;

class RegionImageUploadHandler
{
    private $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function handle(RegionImageUploadCommand $command): void
    {
        $file = null;
        try {
            $file = $this->fileUploadService->uploadFile($command->getFolderPart(), $command->getImage());

            Image::resize($file->getFilePath(), 1000, 500)
                ->save($file->getFilePath(), ['quality' => 100]);

            $this->create(
                $command->getImageId(),
                $command->getTestingId(),
                $command->getFragmentId(),
                $file
            );
        } catch (Exception $exception) {
            if ($file !== null) {
                $this->fileUploadService->deleteFile($command->getFolderPart(), $file->getFileName(), $file->getExtension());
            }
            throw $exception;
        }
    }

    private function create(string $imageId, int $testingId, string $fragmentId, File $file): void
    {
        $image = StorySlideImage::create($imageId, $file->getFileName(), $file->getBaseFolder(), $file->getExtension());
        if (!$image->save()) {
            throw ModelDomainException::create($image);
        }

        \Yii::$app->db->createCommand()
            ->insert('fragment_image', [
                'fragment_id' => $fragmentId,
                'testing_id' => $testingId,
                'image_id' => $image->id,
            ])
            ->execute();
    }
}
