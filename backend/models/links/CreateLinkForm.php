<?php


namespace backend\models\links;


use common\models\StorySlideBlock;
use yii\base\Model;

class CreateLinkForm extends Model
{

    public $slide_id;
    public $title;
    public $href;
    public $type;

    public function __construct(int $slide_id, $config = [])
    {
        $this->slide_id = $slide_id;
        parent::__construct($config);
    }

    public function init()
    {
        $this->type = StorySlideBlock::TYPE_BUTTON;
        parent::init();
    }

    public function rules()
    {
        return [
            [['slide_id', 'title'], 'required'],
            [['slide_id'], 'integer'],
            [['title', 'href'], 'string', 'max' => 255],
            ['href', 'url'],
        ];
    }

    public function createLink()
    {
        $model = StorySlideBlock::create($this->slide_id, $this->title, $this->href);
        return $model->save();
    }

}