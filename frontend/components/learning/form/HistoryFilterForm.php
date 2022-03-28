<?php

namespace frontend\components\learning\form;

use Yii;
use yii\base\Model;

class HistoryFilterForm extends Model
{

    public $date;
    public $action;

    public const ACTION_CHANGE = 'change';
    public const ACTION_PREV = 'prev';
    public const ACTION_NEXT = 'next';

    public function init(): void
    {
        $this->date = date('d.m.Y');
        $this->action = self::ACTION_CHANGE;
        parent::init();
    }

    public function rules(): array
    {
        return [
            [['date'], 'required'],
            [['date'], 'date', 'format' => 'd.m.yyyy'],
            ['action', 'in', 'range' => [self::ACTION_CHANGE, self::ACTION_NEXT, self::ACTION_PREV]],
        ];
    }

    public function getFormattedDate(): string
    {
        return date('Y-m-d', strtotime($this->date));
    }

    /**
     * @throws \Exception
     */
    public function setDateNext(): void
    {
        $this->date = (new \DateTimeImmutable($this->date))->add(\DateInterval::createFromDateString('1 day'))->format('d.m.Y');
    }

    /**
     * @throws \Exception
     */
    public function setDatePrev(): void
    {
        $this->date = (new \DateTimeImmutable($this->date))->add(\DateInterval::createFromDateString('-1 day'))->format('d.m.Y');
    }
}