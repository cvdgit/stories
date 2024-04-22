<?php

declare(strict_types=1);

namespace backend\models\pass_test;

use common\models\StoryTestQuestion;
use yii\base\Model;

class PassTestForm extends Model
{
    public $name;
    public $content;
    public $content_html;
    public $payload;
    public $view;
    public $max_prev_items;
    public $imageFile;

    /** @var int|null */
    private $id;

    /** @var int|null */
    private $testId;
    /**
     * @var StoryTestQuestion|null
     */
    private $model;

    public function __construct(StoryTestQuestion $model = null, $config = [])
    {
        parent::__construct($config);
        $this->model = $model;
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->payload = $model->regions;
            $this->view = $model->sort_view === 0 || $model->sort_view === 1 ? 0 : $model->sort_view;
            $this->testId = $model->story_test_id;
            $this->max_prev_items = $model->max_prev_items;
        }
    }

    public function init(): void
    {
        parent::init();
        $this->name = 'Выберите правильный ответ из вариантов, предложенных в списке';
        $this->view = 0;
        $this->max_prev_items = 5;
    }

    public function rules(): array
    {
        return [
            [['name', 'content'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['content', 'payload'], 'safe'],
            [['view', 'max_prev_items'], 'integer'],
            [['imageFile'], 'image'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'content' => 'Текст с пропусками',
            'view' => 'Показывать',
            'max_prev_items' => 'Возврат на',
            'imageFile' => 'Изображение',
        ];
    }

    public function getViewItems(): array
    {
        return [
            0 => 'Все фрагменты',
            2 => 'Один за раз',
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

    public function getImageUrl(): ?string
    {
        if ($this->model === null) {
            throw new \DomainException("Model is null");
        }
        return $this->model->getImageUrl();
    }

    public function haveImage(): bool
    {
        if ($this->model === null) {
            throw new \DomainException("Model is null");
        }
        return !empty($this->model->image);
    }

    public function getModelId(): int
    {
        if ($this->model === null) {
            throw new \DomainException("Model is null");
        }
        return $this->model->id;
    }
}
