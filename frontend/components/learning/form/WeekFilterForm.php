<?php

namespace frontend\components\learning\form;

use DateTimeImmutable;
use Exception;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class WeekFilterForm extends Model
{
    public $week;
    public $action;

    public const ACTION_PREV = 'prev';
    public const ACTION_NEXT = 'next';

    /** @var \DateTime */
    private $weekStartDate;

    /** @var \DateTime */
    private $weekEndDate;

    public function init(): void
    {
        $this->week = (int) date('w');
        parent::init();
    }

    public function rules(): array
    {
        return [
            [['week'], 'required'],
            [['week'], 'integer'],
            ['action', 'in', 'range' => [self::ACTION_NEXT, self::ACTION_PREV]],
        ];
    }

    /**
     * @throws Exception
     */
    private function calcWeekDates(): void
    {
        if ((int) $this->week === 1) {
            $this->weekStartDate = DateTimeImmutable::createFromFormat(
                'm-d-Y',
                date('m-d-Y', strtotime('-' . $this->week . ' days'))
            );
            $this->weekEndDate = DateTimeImmutable::createFromFormat(
                'm-d-Y',
                date('m-d-Y', strtotime('+' . (6 - $this->week) . ' days'))
            );
            return;
        }
        $year = date('Y');
        $date = new \DateTime('now');
        $date->setISODate($year, $this->week);
        $this->weekStartDate = clone $date;
        $this->weekEndDate = clone $date->modify('+6 days');
    }

    public function search(int $studentId): array
    {
        $this->calcWeekDates();

        $historyQuery = new Query();
        $historyQuery->select([
            'story_id' => 't2.story_id',
            'question_count' => new Expression('SUM(q.weight)'),
            'target_date' => new Expression("DATE_FORMAT(FROM_UNIXTIME(t.created_at + (3 * 60 * 60)), '%Y-%m-%d')"),
        ]);
        $historyQuery->from(['t' => 'user_question_history']);
        $historyQuery->innerJoin(['t2' => 'story_story_test'], 't.test_id = t2.test_id');
        $historyQuery->innerJoin(['q' => 'story_test_question'], 't.entity_id = q.id');
        $historyQuery->where(['t.student_id' => $studentId, 't.correct_answer' => 1]);

        $weekStartDate = $this->weekStartDate->format('Y-m-d');
        $betweenBegin = new Expression("UNIX_TIMESTAMP('$weekStartDate 00:00:00')");
        $weekEndDate = $this->weekEndDate->format('Y-m-d');
        $betweenEnd = new Expression("UNIX_TIMESTAMP('$weekEndDate 23:59:59')");
        $historyQuery->andWhere(['between', 't.created_at + (3 * 60 * 60)', $betweenBegin, $betweenEnd]);

        $historyQuery->groupBy([
            't2.story_id',
            'target_date',
        ]);

        $query = new Query();
        $query->select([
            'story_id' => 't.story_id',
            'story_title' => 't2.title',
            'question_count' => 't.question_count',
            'target_date' => 't.target_date',
        ]);
        $query->from(['t' => $historyQuery]);
        $query->innerJoin(['t2' => 'story'], 't.story_id = t2.id');

        return $query->all();
    }

    /**
     * @return \DateTime
     */
    public function getWeekStartDate(): \DateTimeInterface
    {
        return $this->weekStartDate;
    }

    /**
     * @return \DateTime
     */
    public function getWeekEndDate(): \DateTimeInterface
    {
        return $this->weekEndDate;
    }

    private function getWeeksInYear(int $year): int
    {
        $firstDay = strtotime("$year-01-01");
        $dayOfWeek = (int) date('N', $firstDay);
        if ($dayOfWeek === 4 || ($dayOfWeek === 3 && date('L', strtotime("$year-01-01")))) {
            return 53;
        }
        return 52;
    }

    public function updateWeekDates(): void
    {
        if (!$this->validate()) {
            throw new \DomainException('WeekFilterForm not valid');
        }
        if ($this->action === self::ACTION_NEXT) {
            $this->week++;
            $this->calcWeekDates();
        }
        if ($this->action === self::ACTION_PREV) {
            $this->week--;
            if ($this->week === 0) {
                $this->week = $this->getWeeksInYear((int) date('Y'));
            }
            $this->calcWeekDates();
        }
        $this->action = null;
    }

    public function getWeekDatesText(): string
    {
        $dayStart = $this->weekStartDate->format('d');
        $dayEnd = $this->weekEndDate->format('d');
        $monthStart = $this->weekStartDate->format('F');
        $monthEnd = $this->weekEndDate->format('F');
        Yii::$app->formatter->locale = 'ru-RU';
        if ($monthStart === $monthEnd) {
            return "$dayStart-" . Yii::$app->formatter->asDate($this->weekEndDate->format('d.m.Y'), 'php:d F');
        }
        return Yii::$app->formatter->asDate($this->weekStartDate->format('d.m.Y'), 'php:d F') . ' - ' . Yii::$app->formatter->asDate($this->weekEndDate->format('d.m.Y'), 'php:d F');
    }
}
