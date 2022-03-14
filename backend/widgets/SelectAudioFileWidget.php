<?php

namespace backend\widgets;

use common\models\AudioFile;
use dosamigos\selectize\SelectizeDropDownList;
use yii\helpers\Json;

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
        //$this->loadUrl = ['question/autocomplete'];

/*        if ($this->audioFile !== null) {
            $this->items = [$this->audioFile->id => $this->audioFile->name];
            $this->options = [
                'options' => [
                    $this->audioFile->id => [
                        'data-data' => $this->getOptionData($this->audioFile->id, $this->audioFile->name),
                    ],
                ],
            ];
        }*/

        array_map(function(AudioFile $audioFile) {
            $this->items[$audioFile->id] = $audioFile->name;
        }, AudioFile::find()->orderBy(['name' => SORT_ASC])->all());

        parent::init();
    }

    public function run()
    {
        parent::run();
    }

    private function getOptionData(int $id, string $name): string
    {
        return Json::encode([
            'id' => $id,
            'name' => $name,
        ]);
    }
}