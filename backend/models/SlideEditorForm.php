<?php

namespace backend\models;

use yii;

class SlideEditorForm extends yii\base\Model
{

    public $slides = [];

    public function rules()
    {
        return [
            [['slides'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'slides' => 'Слайд',
        ];
    }

    public function loadSlidesFromBody($body)
    {
        $document = \phpQuery::newDocumentHTML($body);
        $slides = $document->find('section');
        foreach ($slides as $slide) {
            $this->slides[] = pq($slide)->htmlOuter();
        }
    }

}