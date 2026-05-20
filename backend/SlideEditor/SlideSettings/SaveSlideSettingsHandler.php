<?php

declare(strict_types=1);

namespace backend\SlideEditor\SlideSettings;

use common\models\StorySlide;
use DomainException;
use yii\helpers\Json;

class SaveSlideSettingsHandler
{
    public function handle(SaveSlideSettingsCommand $command): void
    {
        $slide = StorySlide::findOne($command->getSlideId());
        if ($slide === null) {
            throw new DomainException('Slide not found');
        }
        $slide->updateSettings($command->getSettings()->asArray());
        if (!$slide->save()) {
            throw new DomainException('Slide save exception');
        }
    }
}
