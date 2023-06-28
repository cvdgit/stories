<?php

declare(strict_types=1);

namespace backend\VideoFromFile\Update;

use common\models\SlideVideo;
use yii\base\Model;

class UpdateFileForm extends Model
{
    public $title;
    public $captions;

    /** @var int */
    private $id;

    /** @var string */
    private $videoUrl;

    /** @var bool */
    private $haveCaptions = false;

    public function __construct(SlideVideo $model, $config = [])
    {
        parent::__construct($config);
        $this->id = $model->id;
        $this->title = $model->title;
        if (count($model->captions) > 0) {
            $this->captions = $model->captions[0]->content;
            $this->haveCaptions = true;
        }
        $this->videoUrl = $model->getUploadedFileUrl('video_id');
    }

    public function rules(): array
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
            ['captions', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'captions' => 'Субтитры',
        ];
    }

    /**
     * @return string
     */
    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isHaveCaptions(): bool
    {
        return $this->haveCaptions;
    }
}
