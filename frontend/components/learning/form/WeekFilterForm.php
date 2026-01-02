<?php

declare(strict_types=1);

namespace frontend\components\learning\form;

use DateTimeImmutable;
use DateTimeInterface;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class WeekFilterForm extends Model
{
    public $year;
    public $week;

    /** @var DateTimeInterface */
    private $weekStartDate;

    /** @var DateTimeInterface */
    private $weekEndDate;

    public function init(): void
    {
        parent::init();
        $this->initDates();
    }

    public function initDates(): void
    {
        $this->week = (int) date('W');
        $this->year = (int) date('Y');
    }

    public function rules(): array
    {
        return [
            [['year', 'week'], 'required'],
            [['year', 'week'], 'integer'],
            ['year', 'default', 'value' => (int) date('W')],
            ['week', 'default', 'value' => (int) date('Y')],
        ];
    }

    public function search(int $studentId): array
    {
        $this->weekStartDate = new DateTimeImmutable();
        $this->weekStartDate = $this->weekStartDate->setISODate((int) $this->year, (int) $this->week);
        $this->weekEndDate = $this->weekStartDate->modify('+6 days');

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

    public function getWeekStartDate(): DateTimeInterface
    {
        return $this->weekStartDate;
    }

    public function getWeekEndDate(): DateTimeInterface
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
        return Yii::$app->formatter->asDate(
                $this->weekStartDate->format('d.m.Y'),
                'php:d F',
            ) . ' - ' . Yii::$app->formatter->asDate($this->weekEndDate->format('d.m.Y'), 'php:d F');
    }

    public function getPrevWeek(): array
    {
        $year = (int) $this->year;
        $week = (int) $this->week - 1;
        if ($week <= 0) {
            $year--;
            $week = $this->getWeeksInYear($year);
        }
        return ['year' => $year, 'week' => $week];
    }

    public function getNextWeek(): array
    {
        $year = (int) $this->year;
        $week = (int) $this->week + 1;
        $weeks = $this->getWeeksInYear($year);
        if ($week > $weeks) {
            $year++;
            $week = 1;
        }
        return ['year' => $year, 'week' => $week];
    }
}
