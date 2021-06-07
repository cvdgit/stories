<?php

namespace backend\models;

use common\models\Story;
use yii\base\Model;

class StoryAccessByLinkForm extends Model
{

    public $access_link = '';

    private $storyModel;

    public function __construct(Story $model, $config = [])
    {
        parent::__construct($config);
        $this->storyModel = $model;
        if ($model->linkAccessAllowed()) {
            $this->access_link = $model->getPreviewUrl();
        }
    }

    public function attributeLabels()
    {
        return [
            'access_link' => 'Ссылка для доступа',
        ];
    }

    public function linkAccessAllowed(): bool
    {
        return $this->storyModel->linkAccessAllowed();
    }

    public function getStoryID(): int
    {
        return $this->storyModel->id;
    }

}