<?php


namespace frontend\widgets;


use backend\models\AudioUploadForm;
use yii\base\Widget;

class StoryAudio extends Widget
{

    public $storyID;

    public function run()
    {
/*        $model = new AudioUploadForm($this->storyID);
        $path = $model->audioFileRelativePath() . '/';
        return $this->render('_audio', [
            'path' => $path,
            'files' => $model->audioFileList(),
        ]);*/
    }

}