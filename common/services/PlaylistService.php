<?php


namespace common\services;


use common\models\Playlist;

class PlaylistService
{

    public function createPlaylist(string $title)
    {
        $model = Playlist::create($title);
        $model->save();
        return $model;
    }

}