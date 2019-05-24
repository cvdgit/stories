<?php

namespace backend\services;

use backend\components\story\reader\HTMLReader;
use backend\models\SlideEditorForm;
use yii;
use yii\web\UploadedFile;
use common\models\Story;
use common\services\StoryService;
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

	public function updateSlide(SlideEditorForm $form)
	{
        $storyModel = Story::findModel($form->story_id);

        $imagePath = $this->uploadImage($form, $storyModel);

        $reader = new HTMLReader($storyModel->body);
        $story = $reader->load();

        $editor = new StoryEditor($story);
        $editor->updateSlide($form->slide_index, $form->text, $form->text_size, $imagePath, $form->button);

        $body = $editor->getStoryMarkup();
        $storyModel->saveBody($body);
	}

}
