<?php

declare(strict_types=1);

namespace backend\VideoFromFile;

use backend\models\SlideVideoSearch;
use backend\models\video\VideoSource;
use yii\base\Action;
use yii\web\Request;

class VideoListAction extends Action
{
    public function run(Request $request): string
    {
        $model = new SlideVideoSearch();
        $dataProvider = $model->search(VideoSource::FILE, $request->get());
        return $this->controller->render('index', [
            'searchModel' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
}
