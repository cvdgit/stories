<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Column\Import;

use yii\base\Model;

class ImportColumnQuestionsForm extends Model
{
    public $firstDigitMin;
    public $firstDigitMax;
    public $secondDigitMin;
    public $secondDigitMax;
    public $sign;
    public $number;

    public function init(): void
    {
        $this->firstDigitMin = 10;
        $this->firstDigitMax = 999;
        $this->secondDigitMin = 10;
        $this->secondDigitMax = 999;
        parent::init();
    }

    public function rules(): array
    {
        return [
            [['firstDigitMin', 'firstDigitMax', 'secondDigitMin', 'secondDigitMax', 'sign', 'number'], 'required'],
            [['firstDigitMin', 'firstDigitMax', 'secondDigitMin', 'secondDigitMax'], 'integer', 'min' => 1],
            ['sign', 'safe'],
            ['number', 'integer'],
        ];
    }
}
