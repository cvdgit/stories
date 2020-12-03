<?php

namespace backend\models\test;

use common\models\StoryTest;
use DomainException;

class CreateForm extends BaseVariantModel
{

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

    public function createTestVariant()
    {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }
        $this->loadParentTest();

        $this->question_params = sprintf('taxonName=%1s;taxonValue=%2s', $this->taxonName, $this->taxonValue);
        $this->wrong_answers_params = $this->createWrongAnswersParams();

        $model = StoryTest::createVariant(
            $this->parentID,
            $this->title,
            $this->header,
            $this->description_text,
            $this->incorrect_answer_text,
            $this->parentTest->question_list_id,
            $this->parentTest->question_list_name,
            $this->question_params,
            $this->wrong_answers_params);
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