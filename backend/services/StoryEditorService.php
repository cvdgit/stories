<?php

namespace backend\services;

use backend\components\book\blocks\Test;
use backend\components\image\EditorImage;
use backend\components\image\SlideImage;
use backend\components\story\AbstractBlock;
use backend\components\story\HTMLBLock;
use backend\components\story\ImageBlock;
use backend\components\story\MentalMapBlock;
use backend\components\story\MentalMapBlockContent;
use backend\components\story\reader\HTMLReader;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\SlideContent;
use backend\components\story\TestBlock;
use backend\components\story\TestBlockContent;
use backend\components\story\VideoBlock;
use backend\components\story\writer\HTMLWriter;
use backend\components\StudyTaskFinalSlide;
use backend\models\editor\BaseForm;
use backend\models\editor\ImageForm;
use backend\models\ImageSlideBlock;
use backend\models\video\VideoSource;
use common\models\LessonBlock;
use common\models\slide\SlideKind;
use common\models\slide\SlideStatus;
use common\models\SlideVideo;
use common\models\StorySlide;
use common\models\StorySlideImage;
use common\models\StoryStoryTest;
use common\models\test\SourceType;
use DomainException;
use common\models\Story;
use yii\base\InvalidConfigException;
use yii\db\Query;
use yii\web\NotFoundHttpException;

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

    public function createSlide(int $storyID, int $currentSlideID = -1, int $lessonId = null): int
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

        if ($lessonId !== null) {
            $this->createLessonBlock($lessonId, $model->id);
        }

        return $model->id;
    }

    private function createLessonBlock(int $lessonId, int $slideId): void
    {
        $lessonBlock = LessonBlock::create($lessonId, $slideId);
        if (!$lessonBlock->save()) {
            throw new DomainException('LessonBlock save exception');
        }
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

    /**
     * @throws InvalidConfigException
     */
    public function createQuestionBlock(array $params): string
    {
        $reader = new HtmlSlideReader('');
        $slide = $reader->load();
        $slide->setView('new-question');
        $block = $slide->createBlock(HTMLBLock::class);
        $block->setContent((new TestBlockContent($params['test-id']))->render());
        $slide->addBlock($block);
        return (new HTMLWriter())->renderSlide($slide);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getSlideWithMentalMapBlockContent(int $slideId, string $mentalMapId): string
    {
        $slide = (new HtmlSlideReader(new SlideContent($slideId, 'mental-map')))->load();
        $block = $slide->createBlock(MentalMapBlock::class);
        $block->setContent((new MentalMapBlockContent($mentalMapId))->render());
        $slide->addBlock($block);
        return (new HTMLWriter())->renderSlide($slide);
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

    public function copySlide(int $slideID, int $lessonId = null): int
    {
        /** @var StorySlide $slide */
        $slide = StorySlide::findSlide($slideID);
        $data = $slide->getSlideOrLinkData();

        $newSlide = StorySlide::createSlide($slide->story_id);
        $newSlide->data = $data;

        Story::insertSlideNumber($slide->story_id, $slide->number);
        $newSlide->number = $slide->number + 1;

        if (!$newSlide->save()) {
            throw new DomainException('copySlide exception');
        }

        if ($lessonId !== null) {
            $this->createLessonBlock($lessonId, $newSlide->id);
        }

        return $newSlide->id;
    }

    public function deleteSlide(StorySlide $slideModel): void
    {
        $slide = $this->processData($slideModel->data);
        foreach ($slide->getBlocks() as $block) {
            if ($block->isVideo()) {
                Story::updateVideo($slideModel->story_id, false);
            }
            if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                ImageSlideBlock::deleteImageBlock($slideModel->id, $block->getId());
            }
            if ($block->getType() === AbstractBlock::TYPE_HTML) {
                /** @var HTMLBLock $block */
                /** @var TestBlockContent $content */
                $content = $block->getContentObject(TestBlockContent::class);
                StoryStoryTest::deleteStoryTest($slideModel->story_id, $content->getTestID());
            }
            if ($block->isTest()) {
                /** @var TestBlock $block */
                StoryStoryTest::deleteStoryTest($slideModel->story_id, $block->getTestID());
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

            /** @var Test $block */
            $testId = $block->getTestId();
            $questionCount = (int)(new Query())
                ->from('story_test_question')
                ->where(['story_test_id' => $testId])
                ->count();
            if ($questionCount === 0) {
                throw new DomainException('Невозможно изменить т.к. в выбранном тесте нет вопросов');
            }

            try {
                $this->storyLinkService->createTestLink($model->story_id, $block->getTestID());
            }
            catch (\Exception $exception) {

            }
        }

        if ($block->typeIs(AbstractBlock::TYPE_VIDEO) || $block->typeIs(AbstractBlock::TYPE_VIDEOFILE)) {
            /** @var VideoBlock $block */
            if ($block->getSource() === VideoSource::YOUTUBE) {
                $videoModel = SlideVideo::findModelByVideoID($block->getVideoId());
            }
            else {
                $videoModel = SlideVideo::findModel(pathinfo($block->getVideoId(), PATHINFO_FILENAME));
            }
            if ($videoModel !== null) {
                $block->setContent($videoModel->title);
                if ($block->getSource() === VideoSource::FILE && $block->isShowCaptions()) {
                    $block->setCaptionsUrl('/video/captions?id=' . $videoModel->id);
                }
            }
        }

        $writer = new HTMLWriter();
        $model->updateData($writer->renderSlide($slide));

        return str_replace('data-src=', 'src=', $writer->renderBlock($block));
    }

    public function createQuizBlock(StorySlide $slideModel, BaseForm $form): string
    {
        return $this->createBlock($slideModel, $form, HTMLBLock::class);
    }

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function createBlock(StorySlide $slideModel, BaseForm $form, string $blockClassName): string
    {
        $slide = (new HtmlSlideReader($slideModel->data))->load();
        $block = $slide->createBlock($blockClassName);

        if ($block->getType() === AbstractBlock::TYPE_HTML) {
            $slide->setView('new-question');
        }

        if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
            /** @var ImageForm $form */
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
            elseif (!empty($form->image_id)) {
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
        } else {
            $block->update($form);
        }

        if ($block->typeIs(AbstractBlock::TYPE_VIDEO) || $block->typeIs(AbstractBlock::TYPE_VIDEOFILE)) {
            /** @var VideoBlock $block */
            if ($block->getSource() === VideoSource::YOUTUBE) {
                $videoModel = SlideVideo::findModelByVideoID($block->getVideoId());
            }
            else {
                $videoModel = SlideVideo::findModel(pathinfo($block->getVideoId(), PATHINFO_FILENAME));
            }
            if ($videoModel !== null) {
                $block->setContent($videoModel->title);
                if ($block->getSource() === VideoSource::FILE && $block->isShowCaptions()) {
                    $block->setCaptionsUrl('/video/captions?id=' . $videoModel->id);
                }
            }
        }

        if ($block->isTest()) {
            /** @var Test $block */
            $testId = $block->getTestId();
            $source = (int)(new Query())
                ->select('source')
                ->from('story_test')
                ->where(['id' => $testId])
                ->scalar();
            if ($source === SourceType::TEST) {
                $questionCount = (int) (new Query())
                    ->from('story_test_question')
                    ->where(['story_test_id' => $testId])
                    ->count();
                if ($questionCount === 0) {
                    throw new DomainException('Невозможно добавить т.к. в выбранном тесте нет вопросов');
                }
            }
        }

        if ($block->isHtmlTest()) {
            /** @var HTMLBLock $block */
            $content = TestBlockContent::createFromHtml($block->getContent());
            $source = (int)(new Query())
                ->select('source')
                ->from('story_test')
                ->where(['id' => $content->getTestID()])
                ->scalar();
            if ($source === SourceType::TEST) {
                $questionCount = (int) (new Query())
                    ->from('story_test_question')
                    ->where(['story_test_id' => $content->getTestID()])
                    ->count();
                if ($questionCount === 0) {
                    throw new DomainException('Невозможно добавить т.к. в выбранном тесте нет вопросов');
                }
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

    public function textFromStory(Story $model): string
    {
        $reader = new HTMLReader($model->slidesData());
        $story = $reader->load();
        $text = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_HEADER) {
                    $text[] = $block->getText();
                }
                if ($block->getType() === AbstractBlock::TYPE_TEXT) {
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

    public function createFinalSlide(int $storyId): StorySlide
    {
        $html = StudyTaskFinalSlide::create();
        return StorySlide::createSlideFull($storyId, $html, null, SlideStatus::VISIBLE, SlideKind::FINAL_SLIDE);
    }

    public function deleteFinalSlide(int $storyId): void
    {
        StorySlide::deleteAll('story_id = :story AND kind = :kind', [':story' => $storyId, ':kind' => SlideKind::FINAL_SLIDE]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function jsonFromStory(string $slideData, string $storyUrl, string $storyTitle): array
    {
        $reader = new HTMLReader($slideData);
        $story = $reader->load();
        $texts = [];
        foreach ($story->getSlides() as $i => $slide) {

            $slideNumber = $i + 1;

            if ($slide->getView() === "new-question") {
                continue;
            }

            //$text = [];
            //$images = [];
            foreach ($slide->getBlocks() as $block) {
                if ($block->getType() === AbstractBlock::TYPE_TEXT || $block->getType() === AbstractBlock::TYPE_HEADER) {
                    $texts[] = $block->getText();
                }
                /*if ($block->getType() === AbstractBlock::TYPE_IMAGE) {
                    $path = $block->getFilePath();
                    if (strpos($path, 'http') === false) {
                        $path = Url::homeUrl() . $path;
                    }
                    $images[] = $path;
                }*/
            }

            /*$content = implode(PHP_EOL, $text);

            if (strlen($content) > 10) {
                $slideModel = StorySlide::findOne($slide->getId());
                if ($slideModel === null) {
                    throw new NotFoundHttpException("Слайд не найден");
                }
                $slides[] = [
                    "story_title" => $storyTitle,
                    "content" => $content,
                    "slide_url" => $storyUrl . ($slideNumber === 1 ? "" : "#/" . $slideNumber),
                    "images" => $images,
                ];
            }*/
        }

        $content = implode(PHP_EOL, $texts);
        $content = htmlentities($content, null, "utf-8");
        $content = str_replace("&nbsp;", " ", $content);
        $content = html_entity_decode($content);
        $content = strip_tags($content);

        return [
            "title" => $storyTitle,
            "content" => $content,
            "url" => $storyUrl,
        ];
    }
}
