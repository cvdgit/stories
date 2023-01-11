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

    /** @var int|null */
    private $id;

    public function __construct(StoryTestQuestion $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            $this->payload = $model->regions;
            $this->view = $model->sort_view;
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
            ['view', 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
            'content' => 'Текст с пропусками',
            'view' => 'Представление',
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
}
