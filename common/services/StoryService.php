<?php

namespace common\services;

use backend\components\image\PowerPointImage;
use backend\components\story\AbstractBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\PowerPointReader;
use backend\components\story\writer\HTMLWriter;
use common\models\NotificationModel;
use common\models\Story;
use common\models\story\StoryStatus;
use common\models\StorySlide;
use DirectoryIterator;
use DomainException;
use backend\components\notification\NewStoryNotification;
use yii;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class StoryService
{

    private $notificationService;

	public function __construct(NotificationService $notificationService)
	{
	    $this->notificationService = $notificationService;
	}

    public function importStoryFromPowerPoint(Story $storyModel): void
    {
        $imagesFolder = $storyModel->getSlideImagesPath(false);
        FileHelper::createDirectory(Yii::getAlias('@public') . $imagesFolder);
        $story = (new PowerPointReader($storyModel->getStoryFilePath(), Yii::getAlias('@public'), $imagesFolder))->load();

        $command = Yii::$app->db->createCommand();
        $imageIDs = array_keys((new Query())
            ->select('image_id')
            ->from('{{%image_slide_block}}')
            ->where(['in', 'slide_id', $storyModel->getSlideIDs()])
            ->indexBy('image_id')
            ->all());
        if (count($imageIDs) > 0) {
            $command->delete('{{%story_slide_image}}', ['in', 'id', $imageIDs])->execute();
        }
        $command->delete('{{%story_slide}}', 'story_id = :story', [':story' => $storyModel->id])->execute();
        $command->delete('{{%story_story_slide_image}}', 'story_id = :story', [':story' => $storyModel->id])->execute();

        $writer = new HTMLWriter();
        $slides = $story->getSlides();
        $image = new PowerPointImage($storyModel->getSlideImagesFolder());

        foreach ($slides as $slide) {

            $slideModel = StorySlide::createSlideFull($storyModel->id, 'init', $slide->getSlideNumber());
            $slideModel->save();

            $slide->setId($slideModel->id);
            $slideModel->updateData($writer->renderSlide($slide));

            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                    /** @var $block ImageBlock */
                    $imageModel = $image->create(Yii::getAlias('@public') . $block->getFilePath());
                    $image->createSlideBlockLink($imageModel->id, $slide->getId(), $block->getId());
                }
            }
        }

        $storyModel->slides_number = count($slides);
        $storyModel->save(false, ['slides_number']);
    }

    public function getCoverPath($cover, $web = false)
    {
        return ($web ? Url::base(true) : Yii::getAlias('@public')) . '/slides_cover/' . $cover;
    }

    public function getImagesFolderPath($model, $web = false)
    {
        return ($web ? '' : Yii::getAlias('@public')) . '/slides/' . $model->story_file;
    }

    public function getStoryImages($model)
    {
        $dir  = opendir($this->getImagesFolderPath($model));
        $images = [];
        while (false !== ($filename = readdir($dir))) {
            if (!in_array($filename, array('.', '..'))) {
                $images[] = $this->getImagesFolderPath($model, true) . '/' . $filename;
            }
        }
        return $images;
    }

    public static function getStoryFilePath($storyFile)
    {
        return Yii::getAlias('@public') . '/slides_file/' . $storyFile;
    }

    protected function getStoryFiles($story)
    {
        $files = [];
        if (!empty($story->cover)) {
            $files[] = $this->getCoverPath($story->cover);
        }
        if (!empty($story->story_file)) {
            $files[] = self::getStoryFilePath($story->story_file);
            $imagesFolder = Yii::getAlias('@public') . '/slides/' . $story->story_file . '/';
            if (file_exists($imagesFolder)) {
                $dir = new DirectoryIterator($imagesFolder);
                foreach ($dir as $file) {
                    if ($file->isFile()) {
                        $files[] = $file->getPathname();
                    }
                }
                $files[] = $imagesFolder;
            }
        }
        return $files;
    }

    private function _deleteFile($file)
    {
        if (file_exists($file)) {
            if (is_dir($file)) {
                rmdir($file);
            }
            else {
                unlink($file);
            }
        }
    }

    public function deleteStoryFiles($story)
    {
        $files = $this->getStoryFiles($story);
        foreach ($files as $fileName) {
            $this->_deleteFile($fileName);
        }
    }

    public function publishStory(Story $model, bool $sendEmail = true): void
    {
        if ($model->isPublished()) {
            throw new DomainException('История уже опубликована');
        }
        if (empty($model->cover)) {
            throw new DomainException('Не установлена обложка');
        }
        if ($model->slides_number === 0) {
            throw new DomainException('В истории отсутствуют слайды');
        }

        if ($sendEmail) {
            $model->storyToPublish();
        }
        else {
            $model->publishStory();
        }

        $notification = new NotificationModel();
        $notification->text = (new NewStoryNotification($model))->render();
        $this->notificationService->sendToAllUsers($notification);
    }

    public function unPublishStory(Story $model): void
    {
        $model->status = StoryStatus::DRAFT;
        $model->save(false, ['status']);
    }

    public function getDefaultStoryView()
    {
        $view = 'book';
        // !Yii::$app->devicedetect->isMobile()
        if (!Yii::$app->user->isGuest) {
            $view = 'slides';
        }
        return $view;
    }

}