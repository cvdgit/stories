<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest;

use yii\base\Model;
use yii\web\UploadedFile;

class RegionImageUploadForm extends Model
{
    /** @var UploadedFile */
    public $image;

    public $testing_id;
    public $fragment_id;

    public function rules(): array
    {
        return [
            [['image', 'fragment_id', 'testing_id'], 'required'],
            ['image', 'file', 'maxSize' => 1024 * 1024 * 20, 'extensions' => ['jpeg', 'jpg', 'png']],
            [['testing_id', 'fragment_id'], 'string'],
        ];
    }
}
