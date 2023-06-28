<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Create;

use backend\models\VideoCaption;
use common\models\SlideVideo;
use common\services\TransactionManager;

class CreateFileHandler
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(CreateFileCommand $command): void
    {
        $this->transactionManager->wrap(static function() use ($command) {

            $video = SlideVideo::createFromFile($command->getUuid(), $command->getName());
            $video->video_id = $command->getVideoFile();
            if (!$video->save()) {
                throw new \DomainException('Video file save exception');
            }

            if ($command->getCaptions() !== null) {
                $captions = VideoCaption::create($video->id, 'Субтитры', 'en', $command->getCaptions());
                if (!$captions->save()) {
                    throw new \DomainException('Video captions save exception');
                }
            }
        });
    }
}
