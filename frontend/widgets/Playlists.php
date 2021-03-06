<?php

namespace frontend\widgets;

use common\models\Playlist;
use yii\base\Widget;

class Playlists extends Widget
{

    public function run()
    {
        $models = Playlist::randomPlaylists();
        if ($models === null) {
            return;
        }
        return $this->render('playlists', [
            'models' => $models,
        ]);
    }
}
