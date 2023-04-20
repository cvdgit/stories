<?php

declare(strict_types=1);

namespace backend\modules\changelog\ChangelogUpdate;

use backend\modules\changelog\models\Changelog;
use backend\modules\changelog\models\ChangelogStatus;
use yii\base\Model;

class UpdateChangelogForm extends Model
{
    public $id;
    public $title;
    public $text;
    public $tags;
    public $created;
    public $status;

    public function __construct(Changelog $model, $config = [])
    {
        parent::__construct($config);
        $this->title = $model->title;
        $this->text = $model->text;
        $this->tags = implode(',', array_map(static function($tag) {
            return $tag->name;
        }, $model->tags));
        $this->created = \Yii::$app->formatter->asDate($model->created_at, 'php:Y-m-d');
        $this->status = $model->status;
    }

    public function rules(): array
    {
        return [
            [['title', 'text'], 'required'],
            [['text'], 'string'],
            [['title'], 'string', 'max' => 255],
            ['created', 'date', 'format' => 'php:Y-m-d'],
            ['tags', 'safe'],
            ['status', 'in', 'range' => ChangelogStatus::getKeys()],
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
