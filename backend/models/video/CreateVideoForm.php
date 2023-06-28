<?php

declare(strict_types=1);

namespace backend\models\video;

use common\models\SlideVideo;
use Ramsey\Uuid\Uuid;
use yii\base\Model;

class CreateVideoForm extends Model
{
    public $title;
    public $video_id;
    public $source;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        $this->source = VideoSource::YOUTUBE;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['video_id', 'title', 'source'], 'required'],
            [['video_id', 'title'], 'string', 'max' => 255],
            [['source'], 'integer'],
            ['source', 'in', 'range' => VideoSource::getTypes()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'video_id' => 'ИД видео Youtube',
        ];
    }

    public function createVideo(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('Model not valid');
        }
        $model = SlideVideo::create(Uuid::uuid4()->toString(), $this->title, $this->video_id, $this->source);
        $model->save();
    }
}
