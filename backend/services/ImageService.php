<?php


namespace backend\services;


use common\models\StorySlideImage;
use Yii;
use yii\helpers\FileHelper;

class ImageService
{

    public function __construct()
    {
    }

    public function createImage(int $slideID, string $collectionAccount, string $collectionID, string $collectionName, string $contentUrl, string $sourceUrl)
    {
        $hash = Yii::$app->security->generateRandomString();
        $folder = Yii::$app->formatter->asDate('now', 'MM-yyyy');
        $model = StorySlideImage::createImage($slideID, $collectionAccount, $collectionID, $collectionName, $hash, $folder, $contentUrl, $sourceUrl);
        $model->save();
        return $model;
    }

    public function downloadImage(string $url, string $imageName, string $imagePath)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $raw = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $ext = FileHelper::getExtensionsByMimeType($info['content_type']);
        $imageFileName = $imageName . '.' . $ext[1];
        $path = $imagePath;
        FileHelper::createDirectory($path);
        $path .= '/' . $imageFileName;

        $fp = fopen($path, 'xb');
        fwrite($fp, $raw);
        fclose($fp);

        return $path;
    }

    public function checkImage(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode === 200;
    }

}