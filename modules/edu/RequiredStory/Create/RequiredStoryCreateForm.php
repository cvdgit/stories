<?php

declare(strict_types=1);

namespace modules\edu\RequiredStory\Create;

use modules\edu\RequiredStory\repo\RequiredStoryStatus;
use yii\base\Model;

class RequiredStoryCreateForm extends Model
{
    public $storyId;
    public $studentId;
    public $startDate;
    public $days;
    public $metadata;
    public $status;
    public $storyStudentFact;

    public function rules(): array
    {
        return [
            [['storyId', 'studentId', 'startDate', 'days', 'metadata', 'status'], 'required'],
            [['storyId', 'studentId'], 'integer'],
            ['days', 'integer', 'min' => 1],
            ['startDate', 'date', 'format' => 'php:Y-m-d'],
            ['metadata', 'string'],
            ['status', 'in', 'range' => RequiredStoryStatus::all()],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'storyId' => 'История',
            'studentId' => 'Ученик',
            'startDate' => 'Дата начала',
            'days' => 'Количество дней',
            'status' => 'Состояние',
        ];
    }
}
