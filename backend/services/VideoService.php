<?php


namespace backend\services;


use Yii;

class VideoService
{

    protected function getVideoInfo(string $videoID)
    {
        $api_key = Yii::$app->params['google.api.key'];
        $api_url = 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics,status&id=' . $videoID . '&key=' . $api_key;
        return json_decode(file_get_contents($api_url), true);
    }

    public function checkVideo(string $videoID): bool
    {
        $data = $this->getVideoInfo($videoID);
        return count($data['items']) > 0;
    }

}