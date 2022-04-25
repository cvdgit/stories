<?php

namespace backend\components\course;

use yii\base\Model;

class LessonForm extends Model
{

    public $id;
    public $uuid;
    public $name;
    public $order;
    public $blocks = [];
    public $type;

    public function rules(): array
    {
        return [
            [['name', 'uuid'], 'string', 'max' => 255],
            [['id', 'order', 'type'], 'integer'],
            ['blocks', 'safe'],
        ];
    }

    public function addBlock(AbstractLessonBlock $block): void
    {
        $this->blocks[] = $block;
    }

    public function fields(): array
    {
        return [
            'id',
            'type',
            'uuid',
            'name',
            'order',
            'blocks',
        ];
    }

    public static function create(int $type, string $uuid, string $name, int $order, int $id = null): self
    {
        $model = new self();
        $model->id = $id;
        $model->type = $type;
        $model->uuid = $uuid;
        $model->name = $name;
        $model->order = $order;
        return $model;
    }

}
