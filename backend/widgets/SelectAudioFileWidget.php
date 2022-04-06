<?php

namespace backend\widgets;

use common\models\AudioFile;
use dosamigos\selectize\SelectizeDropDownList;

class SelectAudioFileWidget extends SelectizeDropDownList
{

    public $audioFile;

    public function init()
    {

        $this->clientOptions = [
            'valueField' => 'id',
            'labelField' => 'name',
            'searchField' => ['name'],
            'maxItems' => 1,
            'maxOptions' => 30,
            'persist' => false,
            'create' => false,
            'openOnFocus' => true,
            'highlight' => true,
            'scrollDuration' => 60,
            'render' => [],
        ];

        array_map(function(AudioFile $audioFile) {
            $this->items[$audioFile->id] = $audioFile->name;
        }, AudioFile::find()->orderBy(['name' => SORT_ASC])->all());

        parent::init();
    }

    public function run()
    {
        parent::run();
    }
}