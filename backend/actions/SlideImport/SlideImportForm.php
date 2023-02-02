<?php

declare(strict_types=1);

namespace backend\actions\SlideImport;

use yii\base\Model;

class SlideImportForm extends Model
{
    public $from_story_id;
    public $to_story_id;
    public $slides;
    public $delete_slides;

    public function rules(): array
    {
        return [
            [['from_story_id', 'to_story_id', 'slides'], 'required'],
            [['from_story_id', 'to_story_id'], 'integer'],
            ['slides', 'each', 'rule' => ['integer']],
            ['delete_slides', 'boolean'],
        ];
    }
}
