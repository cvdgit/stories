<?php


namespace backend\components\story;


use backend\models\editor\ImageForm;
use Yii;

class ImageBlock extends AbstractBlock
{
    /** @var string */
    protected $filePath;

    /** @var float */
    protected $naturalWidth;

    /** @var float */
    protected $naturalHeight;

    protected $action;
    protected $actionStoryID;
    protected $actionSlideID;

    /** @var int */
    protected $back_to_next_slide;

    /** @var string */
    protected $imageSource = '';

    const DEFAULT_IMAGE_WIDTH = 973;
    const DEFAULT_IMAGE_HEIGHT = 720;

    public function setFilePath($filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setImageSize($imagePath, float $imageWidth = 0, float $imageHeight = 0): void
    {
        if ($imagePath === null) {
            return;
        }

        if ($imageWidth === 0 || $imageHeight === 0) {
            [$imageWidth, $imageHeight] = getimagesize($imagePath);
        }

        if ($imageHeight > 0) {

/*            $ratio = $imageWidth / $imageHeight;
            if (self::DEFAULT_IMAGE_WIDTH / self::DEFAULT_IMAGE_HEIGHT > $ratio) {
                $imageWidth = self::DEFAULT_IMAGE_HEIGHT * $ratio;
                $imageHeight = self::DEFAULT_IMAGE_HEIGHT;
            } else {
                $imageHeight = self::DEFAULT_IMAGE_WIDTH / $ratio;
                $imageWidth = self::DEFAULT_IMAGE_WIDTH;
            }*/

            $this->width = $imageWidth . 'px';
            $this->height = $imageHeight . 'px';
        }
    }

    public function setNaturalImageSizeFromFile(string $imagePath): void
    {
        [$this->naturalWidth, $this->naturalHeight] = getimagesize($imagePath);
    }

    public function setNaturalImageSize($imageWidth, $imageHeight): void
    {
        $this->naturalWidth = $imageWidth;
        $this->naturalHeight = $imageHeight;
    }

    /**
     * @return float
     */
    public function getNaturalWidth()
    {
        return $this->naturalWidth;
    }

    /**
     * @return float
     */
    public function getNaturalHeight()
    {
        return $this->naturalHeight;
    }

    public function getValues(): array
    {
        return array_merge([
            'image' => $this->filePath,
            'action' => $this->action,
            'actionStoryID' => $this->actionStoryID,
            'actionSlideID' => $this->actionSlideID,
            'back_to_next_slide' => $this->back_to_next_slide,
        ], parent::getValues());
    }

    /**
     * @param ImageForm $form
     */
    public function update($form)
    {
        $this->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        if (!empty($form->fullImagePath)) {
            $this->setImageSize($form->fullImagePath);
        }
        if (!empty($form->imagePath)) {
            $this->setFilePath($form->imagePath);
        }
        $this->setAction($form->action);
        $this->setActionStoryID($form->actionStoryID);
        $this->setActionSlideID($form->actionSlideID);
        $this->back_to_next_slide = $form->back_to_next_slide;
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('150px');
        $block->setHeight('150px');
        $block->setLeft('50px');
        $block->setTop('50px');
        return $block;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getActionStoryID()
    {
        return $this->actionStoryID;
    }

    /**
     * @param mixed $actionStoryID
     */
    public function setActionStoryID($actionStoryID): void
    {
        $this->actionStoryID = $actionStoryID;
    }

    /**
     * @return mixed
     */
    public function getActionSlideID()
    {
        return $this->actionSlideID;
    }

    /**
     * @param mixed $actionSlideID
     */
    public function setActionSlideID($actionSlideID): void
    {
        $this->actionSlideID = $actionSlideID;
    }

    /**
     * @return int
     */
    public function getBackToNextSlide()
    {
        return $this->back_to_next_slide;
    }

    /**
     * @param int $back_to_next_slide
     */
    public function setBackToNextSlide($back_to_next_slide): void
    {
        $this->back_to_next_slide = $back_to_next_slide;
    }

    /**
     * @return string
     */
    public function getImageSource(): string
    {
        return $this->imageSource;
    }

    /**
     * @param string $imageSource
     */
    public function setImageSource(string $imageSource): void
    {
        $this->imageSource = $imageSource;
    }

    public function delete(): void
    {
        $path = $this->filePath;
        $noFile = false;
        if (strpos($path, '://') !== false) {
            /*
            $query = parse_url($path, PHP_URL_QUERY);
            parse_str($query, $result);
            $imageHash = $result['id'];
            try {
                $image = StorySlideImage::findByHash($imageHash);
                $this->imageService->unlinkImage($image->id, $model->id, $block->getId());
            }
            catch (DomainException $ex) {}
            */
            $noFile = true;
        }
        else {
            $path = Yii::getAlias('@public') . $path;
        }
        if (!$noFile && file_exists($path)) {
            unlink($path);
        }
    }

}