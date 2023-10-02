<?php

declare(strict_types=1);

namespace backend\Testing\Questions\ImageGaps\Update;

use common\models\StoryTestQuestion;
use yii\base\Model;

class UpdateImageGapsForm extends Model
{
    public $name;
    public $image;
    public $payload;
    public $max_prev_items;

    /**
     * @var int
     */
    private $id;
    /**
     * @var int
     */
    private $testId;

    public function __construct(StoryTestQuestion $model, $config = [])
    {
        parent::__construct($config);
        $this->id = $model->id;
        $this->name = $model->name;
        $this->payload = $model->regions;
        $this->testId = $model->story_test_id;
        $this->max_prev_items = $model->max_prev_items;
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            ['name', 'string', 'max' => 256],
            ['image', 'image'],
            [['payload'], 'safe'],
            [['max_prev_items'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'image' => 'Изображение',
            'max_prev_items' => 'Возврат на',
        ];
    }

    public function getMaxPrevItems(): array
    {
        return [
            'Начало',
            '1 элемент',
            '2 элемента',
            '3 элемента',
            '4 элемента',
            '5 элементов',
        ];
    }
}
