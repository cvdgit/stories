<?php


namespace console\controllers;


use backend\models\audio\AudioUploadForm;
use common\models\Story;
use common\models\StorySlide;
use common\services\StoryAudioService;
use http\Exception\RuntimeException;
use Yii;
use yii\console\Controller;
use yii\db\Query;

class AudioController extends Controller
{

    protected $service;

    public function __construct($id, $module, StoryAudioService $service, $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    /*
    public function actionCreateOriginalTrack()
    {
        $query = (new Query())
            ->from('{{%story}}')
            ->where('audio = 1');
        foreach ($query->each() as $story) {

            $trackID = $this->service->createTrack('Оригинальная дорожка', $story['id'], $story['user_id'], 0, 1);

            $sourceFolder = Yii::getAlias('@public') . '/audio/' . $story['id'];
            $targetFolder = $sourceFolder . DIRECTORY_SEPARATOR . $trackID;
            if (!file_exists($targetFolder)) {
                if (!mkdir($concurrentDirectory = $targetFolder) && !is_dir($concurrentDirectory)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
            }

            if (file_exists($sourceFolder)) {
                $dir = opendir($sourceFolder);
                while (false !== ($filename = readdir($dir))) {
                    if (!in_array($filename, array('.', '..'))) {
                        if (!is_dir($sourceFolder . DIRECTORY_SEPARATOR . $filename)) {
                            rename($sourceFolder . DIRECTORY_SEPARATOR . $filename, $targetFolder . DIRECTORY_SEPARATOR . $filename);
                            $this->stdout($filename . ' - OK' . PHP_EOL);
                        }
                    }
                }
            }
        }

        $this->stdout('Done!' . PHP_EOL);
    }
    */

    public function actionRenameAudioFiles()
    {
        $models = Story::find()->audio()->all();
        foreach ($models as $story) {

            foreach ($story->storyAudioTracks as $track) {

                $form = new AudioUploadForm($story->id);
                $form->audioTrackID = $track->id;
                $path = $form->audioFilePath();

                foreach ($form->audioFileList() as $file) {
                    $slideNumber = explode('.', $file)[0];
                    $slide = StorySlide::findSlideByNumber($story->id, $slideNumber + 2);
                    if ($slide === null) {
                        $this->stdout("slide $slideNumber not found" . PHP_EOL);
                    }
                    else {
                        rename($path . DIRECTORY_SEPARATOR . $file, $path . DIRECTORY_SEPARATOR . $slide->id . '.mp3');
                        $this->stdout($slide->id . ' - OK' . PHP_EOL);
                    }
                }
            }
        }
        $this->stdout('Done!' . PHP_EOL);
    }

}