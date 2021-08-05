<?php

namespace backend\models\editor;

use backend\components\image\PowerPointImage;
use backend\components\image\SlideImage;
use backend\models\ImageSlideBlock;
use backend\models\StoryImageLink;
use common\models\Story;
use common\models\StorySlide;
use common\models\StorySlideImage;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class ImageForm extends BaseForm
{

    /* @var UploadedFile */
    public $image;

    public $imagePath;
    public $fullImagePath;

    public $action;
    public $actionStoryID;
    public $actionSlideID;
    public $back_to_next_slide;

    public $story_id;
    public $imageID;
    public $what;

    public $url;

    /** @var StorySlideImage */
    public $imageModel;

    public $image_id;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['image', 'image', 'maxSize' => 7 * 1024 * 1024, 'skipOnError' => false, 'extensions' => ['bmp', 'gif', 'jpg', 'jpeg', 'png']],
            [['action', 'actionSlideID', 'actionStoryID', 'back_to_next_slide', 'story_id', 'image_id'], 'integer'],
            [['imagePath', 'what', 'imageID'], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение на слайде',
            'action' => 'Выполнить действие',
            'actionStoryID' => 'История',
            'actionSlideID' => 'Слайд',
            'back_to_next_slide' => 'Возврат на текущий слайд',
        ]);
    }

    public function afterCreate(StorySlide $slideModel): void
    {
        if (!empty($this->imagePath)) {
            $model = ImageSlideBlock::create($this->imageModel->id, $this->slide_id, $this->block_id);
            $model->save();
        }
    }

    public function resizeSlideImage(string $imagePath, $width, $height): string
    {
        $newImageFileName = $this->createNewImageFileName($imagePath);
        Image::resize($imagePath, $width, $height)->save($newImageFileName, ['quality' => 90]);
        FileHelper::unlink($imagePath);
        return $newImageFileName;
    }

    private function createNewImageFileName(string $imagePath): string
    {
        $parts = pathinfo($imagePath);
        return $parts['dirname'] . DIRECTORY_SEPARATOR . md5(random_int(0, 9999) . time() . random_int(0, 9999)) . '.' . $parts['extension'];
    }

    public function uploadImage(Story $storyModel): void
    {
        $this->image = UploadedFile::getInstance($this, 'image');
        if ($this->image) {
            if (!$this->validate()) {
                throw new \DomainException('Image is not valid.');
            }
            $imagesFolder = $storyModel->getSlideImagesPath();
            FileHelper::createDirectory($imagesFolder);
            $slideImageFileName = md5(random_int(0, 9999) . time() . random_int(0, 9999)) . '.' . $this->image->extension;
            $imagePath = "{$imagesFolder}/$slideImageFileName";
            if ($this->image->saveAs($imagePath)) {
                if ($imagePath !== '') {
                    $slideImage = new SlideImage($imagePath);
                    if ($slideImage->needResize()) {
                        $size = $slideImage->getResizeImageSize();
                        $imagePath = $this->resizeSlideImage($imagePath, $size->getWidth(), $size->getHeight());
                    }
                }
                $this->imageModel = (new PowerPointImage($storyModel->getSlideImagesFolder()))->create($imagePath);
                $this->imagePath = $this->imageModel->imageUrl();
                $this->fullImagePath = $imagePath;
            }
        }
    }

    public function createStoryImageLink(int $storyID): void
    {
        if ($this->imageModel === null) {
            throw new \DomainException('ImageForm imageModel is NULL');
        }
        $linkModel = StoryImageLink::create($storyID, $this->imageModel->id);
        $linkModel->save();
    }
}
