<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest;

use common\models\StorySlideImage;
use yii\base\Action;
use yii\db\Query;
use yii\web\Response;

class ImagesAction extends Action
{
    public function run(int $testing_id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $query = (new Query())->select('*')
            ->from('fragment_image')
            ->innerJoin('story_slide_image', 'fragment_image.image_id = story_slide_image.id')
            ->where(['testing_id' => $testing_id]);

        return ['images' => array_map(static function($row) {

            $data = [
                'id' => $row['hash'],
                'url' => \Yii::$app->urlManagerFrontend->createUrl(['/image/view', 'id' => $row['hash']]),
            ];

            $image = StorySlideImage::findByHash($row['hash']);
            if ($image === null) {
                throw new \RuntimeException('Ошибка при создании файла');
            }

            [$width, $height] = getimagesize($image->getImagePath());
            $data += ['width' => $width, 'height' => $height];

            return $data;
        }, $query->all())];
    }
}
