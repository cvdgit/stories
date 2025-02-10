<?php

declare(strict_types=1);

namespace frontend\Transcriptions;

use yii\base\Model;
use yii\web\UploadedFile;

class TranscriptionsForm extends Model
{
    /** @var UploadedFile */
    public $audio;

    public function rules(): array
    {
        return [
            [['audio'], 'required'],
            ['audio', 'file'],
        ];
    }
}
