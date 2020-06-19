<?php

namespace common\services;

use backend\components\notification\NewStoryAudioNotification;
use backend\components\queue\PublishAudioJob;
use backend\models\audio\AudioUploadForm;
use backend\models\audio\UpdateAudioForm;
use common\models\NotificationModel;
use common\models\Story;
use common\models\StoryAudioTrack;
use common\models\StorySlide;
use DomainException;
use frontend\models\SlideAudio;
use frontend\models\StoryTrackModel;
use Yii;

class StoryAudioService
{

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function joinWavs($wavs)
    {
        $fields = implode('/', ['H8ChunkID', 'VChunkSize', 'H8Format', 'H8Subchunk1ID', 'VSubchunk1Size', 'vAudioFormat', 'vNumChannels', 'VSampleRate', 'VByteRate', 'vBlockAlign', 'vBitsPerSample']);
        $data = '';
        $header = '';
        foreach ($wavs as $wav) {
            $fp = fopen($wav,'rb');
            $header = fread($fp,36);
            $info = unpack($fields, $header);
            if ($info['Subchunk1Size'] > 16) {
                $header .= fread($fp, ($info['Subchunk1Size'] - 16));
            }
            $header .= fread($fp,4);
            $size  = unpack('Vsize', fread($fp,4));
            $size  = $size['size'];
            $data .= fread($fp, $size);
        }
        return $header . pack('V', strlen($data)) . $data;
    }

    public function createTrackFolder(string $folder)
    {
        if (!file_exists($folder)) {
            if (!mkdir($concurrentDirectory = $folder, 0755, true) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }
    }

    public function setSlideAudio(SlideAudio $model)
    {
        $slideModel = StorySlide::findSlide($model->slide_id);

        $data = $this->joinWavs($model->getFiles());

        $audioFileName = StoryTrackModel::getTrackPath($slideModel->story_id, $model->track_id);
        $this->createTrackFolder($audioFileName);
        file_put_contents($audioFileName . DIRECTORY_SEPARATOR . $slideModel->id . '.mp3', $data);

        foreach ($model->getFiles() as $fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    public function getStoryTrack(Story $story, $trackID, $userID)
    {
        $track = null;
        if ($trackID !== null) {
            $track = StoryAudioTrack::findModel((int)$trackID);
            if (!$track->canAccessTrack($userID)) {
                $track = null;
            }
        }
        else {
            $track = $story->getStoryTrack($userID);
        }
        return $track;
    }

    public function publishTrack(StoryAudioTrack $model): void
    {
        if ($model->isPublished()) {
            throw new DomainException('Озвучка уже опубликована');
        }

        $form = new AudioUploadForm($model->story_id);
        $files = $form->audioFileList();
        if (count($files) === 0) {
            throw new DomainException('Файлы озвучки не найдены');
        }

        if ($model->publishAudioTrack()) {

            $storyModel = Story::findModel($model->story_id);
            $storyModel->updateAudioFlag(1);

            /*        Yii::$app->queue->push(new PublishAudioJob([
                        'storyID' => $storyModel->id,
                    ]));*/

            $notification = new NotificationModel();
            $notification->text = (new NewStoryAudioNotification($storyModel))->render();
            $this->notificationService->sendToAllUsers($notification);
        }
    }

    public function unPublishTrack(StoryAudioTrack $model): void
    {
        $model->status = StoryAudioTrack::STATUS_DRAFT;
        $model->save(false, ['status']);

        $storyModel = Story::findModel($model->story_id);
        $storyModel->audio = 0;
        $storyModel->save(false, ['audio']);
    }

}