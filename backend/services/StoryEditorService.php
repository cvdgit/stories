<?php

namespace backend\services;

use yii;
use yii\web\UploadedFile;
use common\models\Story;
use common\services\StoryService;
use backend\components\StoryHtmlReader;
use backend\components\StoryEditor;

class StoryEditorService
{

	protected function uploadImage($form, $model)
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

	public function updateSlide($form)
	{
        $storyModel = Story::findOne($form->story_id);
        $imagePath = $this->uploadImage($form, $storyModel);

        $reader = new StoryHtmlReader();
        $story = $reader->loadStoryFromHtml($storyModel->body);

        $editor = new StoryEditor($story);
        $editor->updateSlide($form->slide_index, $form->text, $form->text_size, $imagePath);

        $body = $editor->getStoryMarkup();
        $storyModel->saveBody($body);
	}

}
