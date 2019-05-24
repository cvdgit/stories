<?php

namespace backend\components;

use backend\components\story\ButtonBlock;
use backend\components\story\ImageBlock;
use backend\components\story\TextBlock;
use backend\components\story\writer\HTMLWriter;
use backend\components\story\Story;
use Yii;

class StoryEditor
{

	protected $story;
	protected $writer;

	public function __construct(Story $story)
	{
		$this->story = $story;
		$this->writer = new HTMLWriter();
	}

	public function getSlideMarkup($slideIndex): string
    {
        $slide = $this->story->getSlide($slideIndex);
        return $this->writer->renderSlide($slide);
	}

	public function setSlideText($slideIndex, $text, $textSize): void
    {
        $slide = $this->story->getSlide($slideIndex);
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) === TextBlock::class) {
                $block->setText(nl2br($text));
                $block->setFontSize($textSize);
            }
        }
	}

	public function setSlideButton($slideIndex, $slideButton)
    {
        $slide = $this->story->getSlide($slideIndex);
        $buttonBlock = new ButtonBlock();
        $buttonBlock->setWidth('290px');
        $buttonBlock->setHeight('50px');
        $buttonBlock->setTop('500px');
        $buttonBlock->setLeft('990px');
        $buttonBlock->setTitle('BUTTON');
        $slide->addBlock($buttonBlock);
    }

	public function setSlideImage($slideIndex, $imagePath)
	{
        $slide = $this->story->getSlide($slideIndex);
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) === ImageBlock::class) {
            	$block->setImageSize(Yii::getAlias('@public') . $imagePath, 0, 0);
                $block->setFilePath($imagePath);
            }
        }
	}

	public function getSlideValues($slideIndex): array
    {
		$slide = $this->story->getSlide($slideIndex);
		$values = ['text' => '', 'image' => ''];
        foreach ($slide->getBlocks() as $block) {
            if (get_class($block) === TextBlock::class) {
                $values['text'] = preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $block->getText());
                $values['text_size'] = $block->getFontSize();
            }
            if (get_class($block) === ImageBlock::class) {
                $values['image'] = $block->getFilePath();
            }
        }
        return $values;
	}

	public function getStoryMarkup(): string
    {
        return $this->writer->renderStory($this->story);
	}

	public function getSlides()
	{
		return $this->story->getSlides();
	}

	public function getStory()
	{
		return $this->story;
	}


	public function updateSlide($slideIndex, $slideText, $slideTextSize, $slideImage = '', $slideButton = ''): void
    {
		$this->setSlideText($slideIndex, $slideText, $slideTextSize);
		if (!empty($slideImage)) {
			$this->setSlideImage($slideIndex, $slideImage);
		}
		if (!empty($slideButton)) {
		    $this->setSlideButton($slideIndex, $slideButton);
        }
	}

}