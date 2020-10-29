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
    public $incorrect_answer_text;

    public $taxonName;
    public $taxonValue;

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
            [['title', 'header', 'taxonName', 'taxonValue'], 'required'],
            [['title', 'header', 'question_params', 'incorrect_answer_text'], 'string', 'max' => 255],
            [['taxonName', 'taxonValue'], 'string', 'max' => 255],
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
            'incorrect_answer_text' => 'Текст неправильного ответа',
            'taxonName' => 'Таксон',
            'taxonValue' => 'Значение таксона',
        ];
    }

    public function createTestVariant()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        $this->loadParentTest();

        $this->question_params = sprintf('taxonName=%1s;taxonValue=%2s', $this->taxonName, $this->taxonValue);

        $model = StoryTest::createVariant(
            $this->parentID,
            $this->title,
            $this->header,
            $this->description_text,
            $this->incorrect_answer_text,
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