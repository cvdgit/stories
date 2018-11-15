<?php

namespace common\services;

use yii;

class StoryDropboxService
{

    public function exportSlideBodyFromDropBox($dropbox_story_filename)
    {
        $dropboxPath = Yii::$app->params['dropboxSlidesPath'] . $dropbox_story_filename . '.html';
        $html = Yii::$app->dropbox->read($dropboxPath);
        $document = \phpQuery::newDocumentHTML($html);
        $images = $document->find('img[data-src]');
        foreach ($images as $image) {
            $src = pq($image)->attr('data-src');
            pq($image)->attr('data-src', '/slides/' . $src);
        }
        return $document->find("div.reveal")->html();
    }

    public function exportSlideImagesFromDropBox($dropbox_story_filename)
    {
        $localFolder = Yii::getAlias('@public') . '/slides/' . $dropbox_story_filename . '/';
        if (!file_exists($localFolder)) {
            mkdir($localFolder, 0777);
        }
        else {
            array_map('unlink', glob($localFolder . "*.jpg"));
        }
        $dropboxFolder = Yii::$app->params['dropboxSlidesPath'] . $dropbox_story_filename;
        $contents = Yii::$app->dropbox->listContents($dropboxFolder);
        foreach ($contents as $content) {
            $data = Yii::$app->dropbox->read($content["path"]);
            file_put_contents($localFolder . $content["basename"], $data);
            $data = null;
        }
    }

}