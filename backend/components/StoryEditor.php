<?php

namespace backend\components;

use backend\components\story\AbstractBlock;
use backend\components\story\ButtonBlock;
use backend\components\story\ImageBlock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TextBlock;
use backend\components\story\TransitionBlock;
use backend\components\story\writer\HTMLWriter;
use backend\components\story\Story;
use backend\models\editor\ButtonForm;
use backend\models\editor\ImageForm;
use backend\models\editor\TextForm;
use backend\models\editor\TransitionForm;
use Yii;

class StoryEditor
{

    /** @var Story */
	protected $story;

	/** @var HTMLWriter */
	protected $writer;

	/** @var HTMLReader */
	protected $reader;

	public function __construct(string $html)
	{
		$this->writer = new HTMLWriter();
		$this->reader = new HTMLReader($html);
        $this->story = $this->reader->load();
	}

	public function getSlideMarkup($slideIndex): string
    {
        $slide = $this->story->getSlide($slideIndex);
        return $this->writer->renderSlide($slide);
	}

	public function setSlideText(TextForm $form): void
    {
        /** @var TextBlock $block */
        $block = $this->findBlockByID($form->slide_index, $form->block_id);
        $block->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $block->setText(nl2br($form->text));
        $block->setFontSize($form->text_size);
	}

	public function setSlideButton(ButtonForm $form): void
    {
        /** @var ButtonBlock $block */
        $block = $this->findBlockByID($form->slide_index, $form->block_id);
        $block->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $block->setText($form->text);
        $block->setFontSize($form->text_size);
        $block->setUrl($form->url);
    }

    public function setSlideTransition(TransitionForm $form): void
    {
        /** @var TransitionBlock $block */
        $block = $this->findBlockByID($form->slide_index, $form->block_id);
        $block->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $block->setText($form->text);
        $block->setFontSize($form->text_size);
        $block->setTransitionStoryId($form->transition_story_id);
        $block->setSlides($form->slides);
    }

	public function setSlideImage(ImageForm $form): void
    {
        /** @var ImageBlock $block */
        $block = $this->findBlockByID($form->slide_index, $form->block_id);
        $block->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        if (!empty($form->fullImagePath)) {
            $block->setImageSize($form->fullImagePath);
        }
        if (!empty($form->imagePath)) {
            $block->setFilePath($form->imagePath);
        }
	}

	public function getBlockValues(AbstractBlock $block): array
    {
        $values = [
            'left' => $block->getLeft(),
            'top' => $block->getTop(),
            'width' => $block->getWidth(),
            'height' => $block->getHeight(),
        ];
        switch (get_class($block)) {
            case TextBlock::class:
                $values['text'] = preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $block->getText());
                $values['text_size'] = $block->getFontSize();
                break;
            case ImageBlock::class:
                $values['image'] = $block->getFilePath();
                break;
            case ButtonBlock::class:
                $values['text'] = $block->getText();
                $values['text_size'] = $block->getFontSize();
                $values['url'] = $block->getUrl();
                break;
            case TransitionBlock::class:
                $values['text'] = $block->getText();
                $values['text_size'] = $block->getFontSize();
                $values['transition_story_id'] = $block->getTransitionStoryId();
                $values['slides'] = $block->getSlides();
                break;
        }
        return $values;
    }

	public function getStoryMarkup(): string
    {
        return $this->writer->renderStory($this->story);
	}

	public function getSlides(): array
    {
		return $this->story->getSlides();
	}

	public function getStory(): Story
    {
		return $this->story;
	}

	public function getSlideBlocksArray(int $slideIndex): array
    {
        $slide = $this->story->getSlide($slideIndex);
        return array_map(function(AbstractBlock $block) {
            return [
                'id' => $block->getId(),
                'type' => $block->getType(),
            ];
        }, $slide->getBlocks());
    }

    protected function createButtonBlock(): ButtonBlock
    {
        $block = new ButtonBlock();
        $block->setWidth('290px');
        $block->setHeight('50px');
        $block->setTop('500px');
        $block->setLeft('990px');
        $block->setText('Название');
        $block->setFontSize('1em');
        $block->setUrl('#');
        return $block;
    }

    protected function createTransitionBlock(): TransitionBlock
    {
        $block = new TransitionBlock();
        $block->setWidth('290px');
        $block->setHeight('50px');
        $block->setTop('600px');
        $block->setLeft('990px');
        $block->setText('Название');
        $block->setFontSize('1em');
        $block->setUrl('#');
        return $block;
    }

    public function createBlock(int $slideIndex, string $blockType): void
    {
        $slide = $this->story->getSlide($slideIndex);
        $block = null;
        if ($blockType === AbstractBlock::TYPE_BUTTON) {
            $block = $this->createButtonBlock();
        }
        if ($blockType === AbstractBlock::TYPE_TRANSITION) {
            $block = $this->createTransitionBlock();
        }
        $slide->addBlock($block);
    }

    /**
     * @param int $slideIndex
     * @param string $blockID
     * @return AbstractBlock
     */
    public function findBlockByID(int $slideIndex, string $blockID): AbstractBlock
    {
        $slide = $this->story->getSlide($slideIndex);
        $blocks = array_filter($slide->getBlocks(), function(AbstractBlock $block) use ($blockID) {
            return ($block->getId() === $blockID);
        });
        return array_shift($blocks);
    }

    public function deleteBlock(int $slideIndex, string $blockID): void
    {
        $slide = $this->story->getSlide($slideIndex);
        $slide->deleteBlock($blockID);
    }

}