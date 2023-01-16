<?php

namespace backend\models\pass_test;

use common\models\StoryTestQuestion;
use yii\base\Model;

class PassTestForm extends Model
{
    public $name;
    public $content;
    public $payload;
    public $view;
    public $max_prev_items;

    /** @var int|null */
    private $id;

    /** @var int|null */
    private $testId;

    public function __construct(StoryTestQuestion $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->payload = $model->regions;
            $this->view = $model->sort_view;
            $this->testId = $model->story_test_id;
            $this->max_prev_items = $model->max_prev_items;
        }
    }

    public function init(): void
    {
        parent::init();
        $this->name = 'Выберите правильный ответ из вариантов, предложенных в списке';
        $this->view = 0;
    }

    public function rules(): array
    {
        return [
            [['name', 'content'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['content', 'payload'], 'safe'],
            [['view', 'max_prev_items'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'content' => 'Текст с пропусками',
            'view' => 'Представление',
            'max_prev_items' => 'Возврат на',
        ];
    }

    public function getViewItems(): array
    {
        return [
            0 => 'Список',
            1 => 'Поле для ввода',
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getTestId(): ?int
    {
        return $this->testId;
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
