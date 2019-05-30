<?php

namespace backend\services;

use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use yii;
use yii\web\UploadedFile;
use common\models\Story;
use common\services\StoryService;
use backend\components\StoryEditor;

class StoryEditorService
{

	protected function uploadImage($form, $model): string
    {
        $imageFile = UploadedFile::getInstance($form, 'image');
        if ($imageFile) {
            $storyService = new StoryService();
            $storyImagesPath = $storyService->getImagesFolderPath($model);
            $slideImageFileName = Yii::$app->security->generateRandomString() . '.' . $imageFile->extension;
            $imagePath = "{$storyImagesPath}/$slideImageFileName";
            if ($imageFile->saveAs($imagePath)) {
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
        $imagePath = $this->uploadImage($form, $model);
        $editor = new StoryEditor($model->body);
        $editor->setSlideImage($form->slide_index, $imagePath);
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

    public function deleteBlock(int $story_id, int $slide_index, string $block_id): void
    {
        $model = Story::findModel($story_id);
        $editor = new StoryEditor($model->body);
        $editor->deleteBlock($slide_index, $block_id);
        $body = $editor->getStoryMarkup();
        $model->saveBody($body);
    }

}
