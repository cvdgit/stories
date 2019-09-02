<?php

namespace backend\models\links;

use common\models\StorySlideBlock;
use yii\base\Model;

class UpdateLinkForm extends Model
{

    public $title;
    public $href;

    public $link_id;

    private $_link;

    public function __construct(int $link_id, $config = [])
    {
        $this->link_id = $link_id;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['link_id', 'title'], 'required'],
            [['title', 'href'], 'string', 'max' => 255],
            ['href', 'url'],
            ['link_id', 'exist', 'targetClass' => StorySlideBlock::class, 'targetAttribute' => ['link_id' => 'id']],
        ];
    }

    public function loadLink()
    {
        $model = $this->getLink();
        $this->title = $model->title;
        $this->href = $model->href;
    }

    public function saveLink()
    {
        $model = $this->getLink();
        $model->title = $this->title;
        $model->href = $this->href;
        return $model->save();
    }

    public function getLink()
    {
        if ($this->_link === null) {
            $this->_link = StorySlideBlock::findBlock($this->link_id);
        }
        return $this->_link;
    }

    public function getSlideID()
    {
        return $this->getLink()->slide_id;
    }

}