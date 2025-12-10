<?php

declare(strict_types=1);

namespace backend\services;

use backend\models\editor\CropImageForm;
use common\models\StorySlideImage;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;

class ImageService
{

    public function createImage(string $collectionAccount, string $collectionID, string $collectionName, string $contentUrl, string $sourceUrl)
    {
        $hash = Yii::$app->security->generateRandomString();
        $folder = Yii::$app->formatter->asDate('now', 'MM-yyyy');
        $model = StorySlideImage::createImage($collectionAccount, $collectionID, $collectionName, $hash, $folder, $contentUrl, $sourceUrl);
        $model->save();
        return $model;
    }

    public function linkImage(int $imageID, int $slideID, string $blockID)
    {
        $command = Yii::$app->db->createCommand();
        $command->insert('{{%image_slide_block}}', [
            'image_id' => $imageID,
            'slide_id' => $slideID,
            'block_id' => $blockID,
        ]);
        return $command->execute();
    }

    public function unlinkImage(int $imageID, int $slideID, string $blockID)
    {
        $command = Yii::$app->db->createCommand();
        $command->delete('{{%image_slide_block}}', 'image_id = :image AND slide_id = :slide AND block_id = :block', [
            'image' => $imageID,
            'slide' => $slideID,
            'block' => $blockID,
        ]);
        return $command->execute();
    }

    /**
     * @throws Exception
     */
    public function downloadImage(string $url, string $savePath): string
    {
        $savePath = FileHelper::normalizePath($savePath);
        FileHelper::createDirectory($savePath);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        $raw = curl_exec($ch);
        if ($raw === false) {
            $errorCode = curl_errno($ch);
            $errorMessage = curl_error($ch);
            throw new \RuntimeException("cURL Error (Code: {$errorCode}): {$errorMessage}");
        }

        $info = curl_getinfo($ch);
        curl_close($ch);

        $ext = FileHelper::getExtensionsByMimeType($info['content_type']);
        if (count($ext) === 0) {
            $imageExtPath = parse_url($url, PHP_URL_PATH);
            $ext = pathinfo($imageExtPath, PATHINFO_EXTENSION);
            if (empty($ext)) {
                throw new \RuntimeException('Неизвестный формат изображения');
            }
        }
        else {
            $ext = $ext[0];
        }

        $imageFileName = md5(random_int(0, 9999) . time() . random_int(0, 9999)) . '.' . $ext;
        $path = $savePath . DIRECTORY_SEPARATOR . $imageFileName;

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

    public function boundImage(int $imageID, int $linkImageID)
    {
        $command = Yii::$app->db->createCommand();
        $command->insert('{{%image_link}}', [
            'image_id' => $imageID,
            'link_image_id' => $linkImageID,
        ]);
        return $command->execute();
    }

    public function cropImage(CropImageForm $form)
    {
        $image = StorySlideImage::findByHash($form->croppedImageID);
        $form->upload($image->getFullPath());
        return $image;
    }

    public function crop(CropImageForm $form, string $path)
    {
        $form->upload($path);
    }
}
