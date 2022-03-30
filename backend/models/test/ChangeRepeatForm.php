<?php

namespace backend\models\test;

use yii\base\Model;
use Yii;

class ChangeRepeatForm extends Model
{

    public $repeat;

    private $testId;

    public function __construct(int $testId, int $repeat = null, $config = [])
    {
        $this->testId = $testId;
        $this->repeat = $repeat;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['repeat', 'integer'],
            ['repeat', 'in', 'range' => TestRepeat::getForRange()],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'repeat' => 'Повторять вопрос',
        ];
    }

    public function updateRepeat(): int
    {
        if (!$this->validate()) {
            throw new \DomainException('ChangeRepeatForm not valid');
        }
        $command = Yii::$app->db->createCommand();
        $command->update('story_test', ['repeat' => $this->repeat], 'id = :id', [':id' => $this->testId]);
        $command->execute();
        return $this->repeat;
    }

    public function getDropdownItems(): array
    {
        return TestRepeat::getForDropdown();
    }
}