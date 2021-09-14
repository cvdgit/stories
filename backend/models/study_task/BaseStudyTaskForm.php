<?php

namespace backend\models\study_task;

use common\services\TransactionManager;
use Yii;
use yii\base\Model;

class BaseStudyTaskForm extends Model
{

    public $title;
    public $description;
    public $status;
    public $slide_ids = [];

    protected $transactionManager;

    public function __construct($config = [])
    {
        $this->transactionManager = Yii::createObject(TransactionManager::class);
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['status'], 'integer'],
            ['status', 'default', 'value' => 0],
            [['title'], 'string', 'max' => 255],
            ['slide_ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
        ];
    }

    public function isNewRecord(): bool
    {
        return $this instanceof CreateStudyTaskForm;
    }

    public function getStorySlides(): array
    {
        return [];
    }

    public function haveStory(): bool
    {
        return false;
    }
}
