<?php


namespace backend\models\editor;


use common\models\StorySlide;
use yii\base\Model;

class SlideSourceForm extends Model
{

    public $source;
    public $slideID;

    public function __construct(int $slideID, $config = [])
    {
        $this->slideID = $slideID;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['source', 'safe'],
        ];
    }

    public function loadSlideSource()
    {
        $slide = StorySlide::findSlide($this->slideID);
        $this->source = $slide->data;
    }

    public function saveSlideSource()
    {
        $slide = StorySlide::findSlide($this->slideID);
        $slide->data = $this->source;
        return $slide->save(false, ['data']);
    }

}