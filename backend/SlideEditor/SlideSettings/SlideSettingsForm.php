<?php

declare(strict_types=1);

namespace backend\SlideEditor\SlideSettings;

use yii\base\Model;

class SlideSettingsForm extends Model
{
    public $speakSlideText;

    public function rules(): array
    {
        return [
            [['speakSlideText'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'speakSlideText' => 'Проговаривать текст на слайде',
        ];
    }

    public function isSpeakSlideText(): bool
    {
        return $this->speakSlideText === '1';
    }
}
