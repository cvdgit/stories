<?php

namespace backend\models\test;

use common\models\StoryTest;
use DomainException;
use yii\base\Model;

class CreateForm extends Model
{

    public $title;
    public $header;
    public $description_text;
    public $question_params;

    private $parentID;
    /** @var StoryTest */
    private $parentTest;

    public function __construct(int $parentID, $config = [])
    {
        $this->parentID = $parentID;
        parent::__construct($config);
    }

    private function loadParentTest()
    {
        $this->parentTest = StoryTest::findModel($this->parentID);
    }

    public function rules()
    {
        return [
            [['title', 'header', 'question_params'], 'required'],
            [['title', 'header', 'question_params'], 'string', 'max' => 255],
            [['description_text'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Название теста',
            'description_text' => 'Описание',
            'header' => 'Заголовок',
            'question_params' => 'Параметры вопроса',
        ];
    }

    public function createTestVariant()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        $this->loadParentTest();
        $model = StoryTest::createVariant(
            $this->parentID,
            $this->title,
            $this->header,
            $this->description_text,
            $this->parentTest->question_list_id,
            $this->parentTest->question_list_name,
            $this->question_params);
        $model->save();
    }

    public function getChildrenTestsAsArray()
    {
        if ($this->parentTest === null) {
            return [];
        }
        return $this->parentTest->getChildrenTestsAsArray();
    }

}