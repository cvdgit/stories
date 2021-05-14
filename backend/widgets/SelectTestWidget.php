<?php

namespace backend\widgets;

use common\models\StoryTest;

class SelectTestWidget extends SelectizeWidget
{

    protected function getData()
    {
        return StoryTest::getTestArray();
    }

    protected function getOptions()
    {
        $data = $this->getData();
        return array_map(static function($key, $value) {
            return [
                'id' => $key,
                'title' => $value,
            ];
        }, array_keys($data), $data);
    }

}