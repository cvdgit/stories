<?php

namespace backend\services;

use backend\components\QuestionHTML;
use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use common\models\StorySlide;
use common\models\StorySlideBlock;
use common\models\StoryTestQuestion;
use DomainException;
use yii;
use yii\db\Query;
use yii\helpers\Html;
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

    public function deleteBlock(int $slideID, string $blockID)
    {
        $model = StorySlide::findSlide($slideID);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        $slide->deleteBlock($blockID);
        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);
        $model->data = $html;
        $model->save(false, ['data']);

        $haveVideo = $this->haveVideoBlock($model->story_id);
        Story::updateVideo($model->story_id, $haveVideo ? 1 : 0);
    }

    protected function haveVideoBlock(int $storyID)
    {
        $model = Story::findModel($storyID);
        $haveVideo = false;
        foreach ($model->storySlides as $slideModel) {
            $reader = new HtmlSlideReader($slideModel->data);
            $slide = $reader->load();
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_VIDEO) {
                    $haveVideo = true;
                    break;
                }
            }
            if ($haveVideo) {
                break;
            }
        }
        return $haveVideo;
    }

    protected function updateSlideNumbers(int $storyID, int $targetSlideNumber)
    {
        $slides = (new Query())->from('{{%story_slide}}')
            ->select(['id', 'number'])
            ->where('story_id = :story', [':story' => $storyID])
            ->orderBy(['number' => SORT_ASC])
            ->indexBy('id')
            ->all();
        $command = Yii::$app->db->createCommand();
        foreach ($slides as $slideID => $slide) {
            if ($slide['number'] > $targetSlideNumber) {
                $slides[$slideID]['number']++;
                $command->update('{{%story_slide}}', ['number' => $slides[$slideID]['number']], 'id = :id', [':id' => $slideID]);
                $command->execute();
            }
        }
    }

    public function createSlide(int $storyID, int $currentSlideID = -1): int
    {
        $reader = new HtmlSlideReader('');
        $slide = $reader->load();
        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);

        $model = StorySlide::createSlide($storyID);
        $model->data = $html;

        if ($currentSlideID !== -1) {
            $currentSlide = StorySlide::findSlideByID($currentSlideID);
            $this->updateSlideNumbers($storyID, $currentSlide->number);
            $model->number = $currentSlide->number + 1;
        }

        $model->save();

        return $model->id;
    }

    public function createSlideLink(int $storyID, int $linkSlideID, int $currentSlideID = -1): int
    {
        $model = StorySlide::createSlide($storyID);
        $model->kind = StorySlide::KIND_LINK;
        $model->link_slide_id = $linkSlideID;
        $model->data = 'link';

        if ($currentSlideID !== -1) {
            $currentSlide = StorySlide::findSlideByID($currentSlideID);
            $this->updateSlideNumbers($storyID, $currentSlide->number);
            $model->number = $currentSlide->number + 1;
        }

        if (!$model->save()) {
            throw new DomainException(implode('<br>', $model->firstErrors));
        }
        return $model->id;
    }

    public function createSlideQuestion(int $storyID, int $questionID, int $currentSlideID = -1)
    {

        $reader = new HtmlSlideReader('');
        $slide = $reader->load();
        $slide->setView('question');

        /** @var HTMLBLock $block */
        $block = $slide->createBlock(HTMLBLock::class);
        $question = StoryTestQuestion::findModel($questionID);
        $content = (new QuestionHTML($question))->loadHTML();
        $block->setContent($content);
        $slide->addBlock($block);

        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);

        $model = StorySlide::createSlide($storyID);
        $model->kind = StorySlide::KIND_QUESTION;
        $model->data = $html;

        if ($currentSlideID !== -1) {
            $currentSlide = StorySlide::findSlideByID($currentSlideID);
            $this->updateSlideNumbers($storyID, $currentSlide->number);
            $model->number = $currentSlide->number + 1;
        }

        $model->save();

        return $model->id;
    }

    public function copySlide(int $slideID): int
    {
        $slide = StorySlide::findSlide($slideID);
        $data = $slide->data;
        if ($slide->isLink()) {
            $linkSlide = StorySlide::findSlide($slide->link_slide_id);
            $data = $linkSlide->data;
        }
        $newSlide = StorySlide::createSlide($slide->story_id);
        $newSlide->data = $data;

        $this->updateSlideNumbers($slide->story_id, $slide->number);
        $newSlide->number = $slide->number + 1;

        $newSlide->save();
        return $newSlide->id;
    }

    public function deleteSlide(int $slideID)
    {
        StorySlide::deleteSlide($slideID);
    }

    public function updateBlock($form)
    {
        $model = StorySlide::findSlide($form->slide_id);
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

    public function newUpdateBlock($form)
    {
        $model = StorySlideBlock::findBlock($form->block_id);
        $model->title = $form->text;
        $model->href = $form->url;
        return $model->save();
    }

    public function generateBookStoryHtml(Story $model)
    {
        $reader = new HTMLReader($model->slidesData(true));
        $story = $reader->load();
        $html = '';
        foreach ($story->getSlides() as $slide) {
            $text = '';
            $image = '';
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_TEXT) {
                    $text = $block->getText();
                }
                if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                    $image = $block->getFilePath();
                }
            }
            $content = '';

            if ($image !== '') {
                $content .= Html::tag('div', Html::img(null, ['data-src' => '{IMAGE}', 'width' => '100%', 'height' => '100%', 'class' => 'lazy']), ['class' => '{CLASS}']);
                $content = strtr($content, [
                    '{IMAGE}' => $image,
                ]);
            }

            if ($text !== '') {
                $content .= Html::tag('div', Html::tag('p', '{TEXT}'), ['class' => '{CLASS}']);
                $content = strtr($content, [
                    '{TEXT}' => $text,
                ]);
            }

            $colClass = 'col-lg-6';
            if (($text !== '' && $image === '') || ($text === '' && $image !== '')) {
                $colClass = 'col-lg-12';
            }
            $content = strtr($content, [
                '{CLASS}' => $colClass,
            ]);

            if ($content !== '') {
                $html .= Html::tag('section', Html::tag('div', $content, ['class' => 'row']));
            }
        }
        return $html;
    }

    public function textFromStory(Story $model)
    {
        $reader = new HTMLReader($model->slidesData());
        $story = $reader->load();
        $text = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_TEXT) {
                    $text[] = $block->getText();
                }
                if ($block->getType() === AbstractBlock::TYPE_HEADER) {
                    $text[] = $block->getText();
                }
            }
        }
        return implode(PHP_EOL, $text);
    }

}
