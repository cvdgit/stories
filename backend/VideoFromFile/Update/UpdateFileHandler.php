<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Update;

use backend\models\VideoCaption;
use common\models\SlideVideo;
use common\services\TransactionManager;

class UpdateFileHandler
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function handle(UpdateFileCommand $command): void
    {
        $video = SlideVideo::findOne($command->getId());
        if ($video === null) {
            throw new \DomainException('Video not found');
        }

        $this->transactionManager->wrap(static function() use ($video, $command) {

            $video->title = $command->getName();
            if (!$video->save()) {
                throw new \DomainException('Video file save exception');
            }

            if ($command->getCaptions() === null) {
                if (count($video->captions) > 0) {
                    VideoCaption::deleteAll(['video_id' => $video->id]);
                }
            } else {
                if (count($video->captions) === 0) {
                    $captions = VideoCaption::create($video->id, 'Субтитры', 'en', $command->getCaptions());
                } else {
                    $captions = $video->captions[0];
                    $captions->content = $command->getCaptions();
                }

                if (!$captions->save()) {
                    throw new \DomainException('Video captions save exception');
                }
            }
        });
    }
}
