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

    const DEFAULT_IMAGE_WIDTH = 973;
    const DEFAULT_IMAGE_HEIGHT = 720;

    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function setImageSize(string $imagePath, float $imageWidth = 0, float $imageHeight = 0): void
    {

        if ($imageWidth === 0 || $imageHeight === 0) {
            [$imageWidth, $imageHeight] = getimagesize($imagePath);
        }

        if ($imageHeight > 0) {

            $ratio = $imageWidth / $imageHeight;
            if (self::DEFAULT_IMAGE_WIDTH / self::DEFAULT_IMAGE_HEIGHT > $ratio) {
                $imageWidth = self::DEFAULT_IMAGE_HEIGHT * $ratio;
                $imageHeight = self::DEFAULT_IMAGE_HEIGHT;
            } else {
                $imageHeight = self::DEFAULT_IMAGE_WIDTH / $ratio;
                $imageWidth = self::DEFAULT_IMAGE_WIDTH;
            }

            $this->width = $imageWidth . 'px';
            $this->height = $imageHeight . 'px';
        }
    }

    public function setNaturalImageSizeFromFile(string $imagePath): void
    {
        [$this->naturalWidth, $this->naturalHeight] = getimagesize($imagePath);
    }

    public function setNaturalImageSize(float $imageWidth, float $imageHeight): void
    {
        $this->naturalWidth = $imageWidth;
        $this->naturalHeight = $imageHeight;
    }

    /**
     * @return float
     */
    public function getNaturalWidth(): float
    {
        return $this->naturalWidth;
    }

    /**
     * @return float
     */
    public function getNaturalHeight(): float
    {
        return $this->naturalHeight;
    }

    public function getValues(): array
    {
        return array_merge([
            'image' => $this->filePath,
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
    }

}