<?php

declare(strict_types=1);

namespace backend\widgets;

use backend\models\WordListAsTextForm;
use yii\base\Widget;

class WordEditWidget extends Widget
{
    public $modelAttribute;
    public $modelAttributeValue;
    public $target;

    public function run(): string
    {
        $model = new WordListAsTextForm();
        $model->word_list_id = $this->modelAttributeValue;
        return $this->render('word-edit', [
            'model' => $model,
            'target' => $this->target,
        ]);
    }
}
