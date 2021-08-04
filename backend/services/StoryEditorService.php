<?php

namespace backend\services;

use backend\components\image\EditorImage;
use backend\components\image\SlideImage;
use backend\components\story\AbstractBlock;
use backend\components\story\BlockType;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\TestBlockContent;
use backend\components\story\VideoBlock;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\BaseForm;
use backend\models\video\VideoSource;
use common\models\SlideVideo;
use common\models\StorySlide;
use common\models\StorySlideImage;
use DomainException;
use common\models\Story;

class StoryEditorService
{

    private $imageService;
    private $storyLinkService;
    private $imageResizeService;

    public function __construct(ImageService $imageService, StoryLinksService $storyLinkService, ImageResizeService $imageResizeService)
    {
        $this->imageService = $imageService;
        $this->storyLinkService = $storyLinkService;
        $this->imageResizeService = $imageResizeService;
    }

	/**
    public function deleteBlock(int $slideID, string $blockID)
    {
        $model = StorySlide::findSlide($slideID);

        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();

        $block = $slide->findBlockByID($blockID);
        if ($block->isTest()) {
            $this->storyLinkService->deleteTestLink($model->story_id, $block->getTestID());
        }

        $slide->deleteBlock($blockID);

        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);

        $model->data = $html;
        $model->save(false);

        $haveVideo = $this->haveVideoBlock($model->story_id);
        Story::updateVideo($model->story_id, $haveVideo ? 1 : 0);
    }
    */

    /**
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
    */

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
            $model->number = $currentSlide->number + 1;
            Story::insertSlideNumber($storyID, $currentSlide->number);
        }
        $model->save(false);
        return $model->id;
    }

    public function createSlideLink(int $storyID, int $linkSlideID, int $currentSlideID = null): int
    {
        $model = StorySlide::createSlide($storyID);
        $model->kind = StorySlide::KIND_LINK;
        $model->link_slide_id = $linkSlideID;
        $model->data = 'link';
        if ($currentSlideID !== null) {
            $currentSlide = StorySlide::findSlideByID($currentSlideID);
            $model->number = $currentSlide->number + 1;
            Story::insertSlideNumber($storyID, $currentSlide->number);
        }
        if (!$model->save()) {
            throw new DomainException(implode('<br>', $model->firstErrors));
        }
        return $model->id;
    }

    /*public function createSlideQuestion(int $storyID, int $questionID, int $currentSlideID = -1)
    {

        $reader = new HtmlSlideReader('');
        $slide = $reader->load();
        $slide->setView('question');

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
    }*/

    public function createQuestionBlock(array $params)
    {
        $reader = new HtmlSlideReader('');
        $slide = $reader->load();
        $slide->setView('new-question');
        /** @var HTMLBLock $block */
        $block = $slide->createBlock(HTMLBLock::class);
        $block->setContent((new TestBlockContent($params['test-id']))->render());
        $slide->addBlock($block);
        $writer = new HTMLWriter();
        return $writer->renderSlide($slide);
    }

    /*public function newCreateSlideQuestion(int $storyID, array $params)
    {
        $model = StorySlide::createSlide($storyID);
        $model->kind = StorySlide::KIND_QUESTION;
        $model->data = $this->createQuestionBlock($params);
        // $this->updateSlideNumbers($currentSlideModel->story_id, $currentSlideModel->number);
        $model->number = 1;
        $model->save();
        return $model->id;
    }*/

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

        Story::insertSlideNumber($slide->story_id, $slide->number);
        $newSlide->number = $slide->number + 1;

        $newSlide->save();
        return $newSlide->id;
    }

    public function deleteSlide(StorySlide $slideModel): void
    {
        $slide = $this->processData($slideModel->data);
        foreach ($slide->getBlocks() as $block) {
            if ($block->isVideo()) {
                Story::updateVideo($slideModel->story_id, false);
            }
        }
        $slideModel->delete();
    }

    public function updateBlock($form): string
    {
        $model = StorySlide::findSlide($form->slide_id);
        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        $block = $slide->findBlockByID($form->block_id);

        //if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            //$storyModel = Story::findModel($model->story_id);
            //$this->uploadImage($form, $storyModel);
        //}
        if ($block->isHtmlTest()) {
            $block->setContent((new TestBlockContent($form->test_id, $form->required))->render());
        }
        else {
            $block->update($form);
        }
        if ($block->isTest()) {
            try {
                $this->storyLinkService->createTestLink($model->story_id, $block->getTestID());
            }
            catch (\Exception $exception) {

            }
        }

        if (BlockType::isVideo($block) || BlockType::isVideoFile($block)) {
            /** @var VideoBlock $block */
            if ($block->getSource() === VideoSource::YOUTUBE) {
                $videoModel = SlideVideo::findModelByVideoID($block->getVideoId());
            }
            else {
                $videoModel = SlideVideo::findModel(pathinfo($block->getVideoId(), PATHINFO_FILENAME));
            }
            if ($videoModel !== null) {
                $block->setContent($videoModel->title);
            }
        }

        $writer = new HTMLWriter();
        $model->updateData($writer->renderSlide($slide));

        return str_replace('data-src=', 'src=', $writer->renderBlock($block));
    }

    public function createBlock(StorySlide $slideModel, BaseForm $form, string $blockClassName): string
    {
        $slide = (new HtmlSlideReader($slideModel->data))->load();
        $block = $slide->createBlock($blockClassName);

        if ($block->getType() === AbstractBlock::TYPE_HTML) {
            $slide->setView('new-question');
        }

        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            $storyModel = Story::findModel($slideModel->story_id);
            if (!empty($form->url)) {

                $image = new EditorImage();
                $imagePath = $this->imageService->downloadImage($form->url, $image->getImagePath());

                $slideImage = new SlideImage($imagePath);
                if ($slideImage->needResize()) {
                    $size = $slideImage->getResizeImageSize();
                    $imagePath = $form->resizeSlideImage($imagePath, $size->getWidth(), $size->getHeight());
                }

                $form->imageModel = $image->create($imagePath);
                $form->imagePath = $form->imageModel->imageUrl();
                $form->fullImagePath = $imagePath;
            }
            else if (!empty($form->image_id)) {
                $form->imageModel = StorySlideImage::findModel($form->image_id);
                $form->imagePath = $form->imageModel->imageUrl();
                $form->fullImagePath = $form->imageModel->getImagePath();
            }
            else {
                $form->uploadImage($storyModel);
            }
            if ($form->imageModel !== null) {
                $block->setBlockAttribute('data-image-id', $form->imageModel->id);
            }
        }
        if ($block->isHtmlTest()) {
            $block->setContent((new TestBlockContent($form->test_id, $form->required))->render());
        }
        else {
            $block->update($form);
        }
        if (BlockType::isVideo($block) || BlockType::isVideoFile($block)) {
            /** @var VideoBlock $block */
            if ($block->getSource() === VideoSource::YOUTUBE) {
                $videoModel = SlideVideo::findModelByVideoID($block->getVideoId());
            }
            else {
                $videoModel = SlideVideo::findModel(pathinfo($block->getVideoId(), PATHINFO_FILENAME));
            }
            if ($videoModel !== null) {
                $block->setContent($videoModel->title);
            }
        }

        $form->block_id = $block->getId();

        $slide->addBlock($block);
        $writer = new HTMLWriter();
        $slideModel->updateData($writer->renderSlide($slide));
        return str_replace('data-src=', 'src=', $writer->renderBlock($block));
    }

    /*public function newUpdateBlock($form)
    {
        $model = StorySlideBlock::findBlock($form->block_id);
        $model->title = $form->text;
        $model->href = $form->url;
        return $model->save(false);
    }*/

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

    public function addImageBlockToSlide(int $slideID, ImageBlock $block)
    {
        $model = StorySlide::findSlide($slideID);

        $reader = new HtmlSlideReader($model->data);
        $slide = $reader->load();
        $slide->addBlock($block);

        $writer = new HTMLWriter();
        $html = $writer->renderSlide($slide);

        $model->data = $html;
        $model->save(false);
    }

    public function processData(string $data): Slide
    {
        return (new HtmlSlideReader($data))->load();
    }
}
