<?php


namespace backend\models\editor;


use common\models\StorySlide;
use yii\base\Model;

class SlideSourceForm extends Model
{

    public $source;

    public $storyID;
    public $slideNumber;

    public function __construct(int $storyID, int $slideNumber, $config = [])
    {
        $this->storyID = $storyID;
        $this->slideNumber = $slideNumber;
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
        $slide = StorySlide::findSlide($this->storyID, $this->slideNumber);
        $this->source = $slide->data;
    }

    public function saveSlideSource()
    {
        $slide = StorySlide::findSlide($this->storyID, $this->slideNumber);
        $slide->data = $this->source;
        return $slide->save(false, ['data']);
    }

}