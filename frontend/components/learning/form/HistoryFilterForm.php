<?php

declare(strict_types=1);

namespace frontend\components\learning\form;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class HistoryFilterForm extends Model
{
    public $date;
    public $hours;

    /** @var DateTimeInterface */
    private $targetDate;

    public function init(): void
    {
        $this->initDates();
        parent::init();
    }

    public function initDates(): void
    {
        $this->date = date('d.m.Y');
        $this->hours = 60;
    }

    public function rules(): array
    {
        return [
            [['date', 'hours'], 'required'],
            [['date'], 'date', 'format' => 'd.m.yyyy'],
            ['hours', 'integer'],
            ['hours', 'in', 'range' => array_keys($this->getHoursDropdown())],
        ];
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function search(int $studentId): array
    {
        $this->targetDate = new DateTimeImmutable($this->date);

        $historyQuery = new Query();
        $hourExpression = new Expression("hour(FROM_UNIXTIME(t.created_at + (3 * 60 * 60)))");
        $minuteExpression = new Expression("minute(FROM_UNIXTIME(t.created_at + (3 * 60 * 60))) DIV $this->hours");
        $historyQuery->select([
            'story_id' => 't2.story_id',
            'question_count' => new Expression('SUM(q.weight)'),
            'hour' => $hourExpression,
            'minute_div' => $minuteExpression,
        ]);
        $historyQuery->from(['t' => 'user_question_history']);
        $historyQuery->innerJoin(['t2' => 'story_story_test'], 't.test_id = t2.test_id');
        $historyQuery->innerJoin(['q' => 'story_test_question'], 't.entity_id = q.id');
        $historyQuery->where(['t.student_id' => $studentId, 't.correct_answer' => 1]);

        $betweenBegin = new Expression("UNIX_TIMESTAMP('{$this->targetDate->format('Y-m-d')} 00:00:00')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('{$this->targetDate->format('Y-m-d')} 23:59:59')");
        $historyQuery->andWhere(['between', 't.created_at + (3 * 60 * 60)', $betweenBegin, $betweenEnd]);

        $historyQuery->groupBy([
            't2.story_id',
            $hourExpression,
            $minuteExpression,
        ]);
        $historyQuery->orderBy([
            'hour' => SORT_ASC,
            'minute_div' => SORT_ASC,
        ]);

        $query = new Query();
        $query->select([
            'story_id' => 't.story_id',
            'story_title' => 't2.title',
            'question_count' => 't.question_count',
            'hour' => 't.hour',
            'minute_div' => 't.minute_div',
        ]);
        $query->from(['t' => $historyQuery]);
        $query->innerJoin(['t2' => 'story'], 't.story_id = t2.id');

        return $query->all();
    }

    public function getHoursDropdown(): array
    {
        return [60 => '1 час', 30 => '30 минут', 20 => '20 минут'];
    }

    public static function createTimes(int $interval): array
    {
        $times = [];
        for ($i = 0; $i <= 23; $i++) {
            $hour = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            for ($j = 0; $j < 60 / $interval; $j++) {
                $minute = $interval * $j;
                $time = [
                    'time' => $hour . ':' . ($minute === 0 ? '00' : $minute),
                    'hour' => $i,
                    'minute_div' => $minute === 60 ? 0 : $minute / $interval,
                ];
                $times[] = $time;
            }
        }
        return $times;
    }

    public function getPrevDate(): array
    {
        $prevDate = $this->targetDate->add(DateInterval::createFromDateString('-1 day'));
        return ['date' => $prevDate->format('d.m.Y')];
    }

    public function getNextDate(): array
    {
        $nextDate = $this->targetDate->add(DateInterval::createFromDateString('+1 day'));
        return ['date' => $nextDate->format('d.m.Y')];
    }
}
