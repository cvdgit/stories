<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogCreate;

use backend\modules\changelog\models\ChangelogStatus;
use yii\base\Model;

class CreateChangelogForm extends Model
{
    public $title;
    public $text;
    public $created;
    public $tags;
    public $status;

    public function rules(): array
    {
        return [
            [['title', 'text', 'created', 'status'], 'required'],
            [['text'], 'string'],
            [['title'], 'string', 'max' => 255],
            ['created', 'date', 'format' => 'php:Y-m-d'],
            ['status', 'in', 'range' => ChangelogStatus::getKeys()],
            ['tags', 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Заголовок',
            'text' => 'Текст',
            'created' => 'Дата создания',
            'tags' => 'Тэги',
            'status' => 'Статус',
        ];
    }
}
