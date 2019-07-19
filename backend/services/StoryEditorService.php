<?php

namespace backend\services;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use common\models\StorySlide;
use yii;
use yii\web\UploadedFile;
use common\models\Story;
use common\services\StoryService;
use backend\components\StoryEditor;

class StoryEditorService
{

	protected function uploadImage(ImageForm $form, $model): string
    {
        $imageFile = UploadedFile::getInstance($form, 'image');
        if ($imageFile) {
            $storyService = new StoryService();
            $storyImagesPath = $storyService->getImagesFolderPath($model);
            $slideImageFileName = Yii::$app->security->generateRandomString() . '.' . $imageFile->extension;
            $imagePath = "{$storyImagesPath}/$slideImageFileName";
            if ($imageFile->saveAs($imagePath)) {
                $form->imagePath = $storyService->getImagesFolderPath($model, true) . '/' . $slideImageFileName;
                $form->fullImagePath = Yii::getAlias('@public') . $form->imagePath;
            	return $storyService->getImagesFolderPath($model, true) . '/' . $slideImageFileName;
        	}
        }
        return '';
	}

	public function updateSlideText(TextForm $form): void
    {
        $model = Story::findModel($form->story_id);
        $editor = new StoryEditor($model->body);
        $editor->setSlideText($form);
        $body = $editor->getStoryMarkup();
        $model->saveBody($body);
    }

    public function updateSlideImage(ImageForm $form): void
    {
        $model = Story::findModel($form->story_id);

        $editor = new StoryEditor($model->body);

        $this->uploadImage($form, $model);
        $editor->setSlideImage($form);

        $body = $editor->getStoryMarkup();
        $model->saveBody($body);
    }

    public function updateSlideButton(ButtonForm $form): void
    {
        $model = Story::findModel($form->story_id);
        $editor = new StoryEditor($model->body);
        $editor->setSlideButton($form);
        $body = $editor->getStoryMarkup();
        $model->saveBody($body);
    }

    public function updateSlideTransition(TransitionForm $form): void
    {
        $model = Story::findModel($form->story_id);
        $editor = new StoryEditor($model->body);
        $editor->setSlideTransition($form);
        $body = $editor->getStoryMarkup();
        $model->saveBody($body);
    }

    public function deleteBlock(int $storyID, int $slideNumber, string $blockID)
    {
        $model = StorySlide::findSlide($storyID, $slideNumber);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        $slide->deleteBlock($blockID);
        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);
        $model->data = $html;
        return $model->save(false, ['data']);
    }

    public function deleteSlide(int $storyID, int $slideNumber)
    {
        StorySlide::deleteSlide($storyID, $slideNumber);
    }

    public function updateBlock($form)
    {
        $model = StorySlide::findSlide($form->story_id, $form->slide_index);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        $block = $slide->findBlockByID($form->block_id);
        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            $storyModel = Story::findModel($model->story_id);
            $this->uploadImage($form, $storyModel);
        }
        $block->update($form);
        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);
        $model->data = $html;
        return $model->save(false, ['data']);
    }

}
