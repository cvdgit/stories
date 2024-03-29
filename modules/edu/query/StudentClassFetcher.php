<?php

declare(strict_types=1);

namespace modules\edu\query;

use yii\db\Query;

class StudentClassFetcher
{
    /**
     * Возвращает ИД класса студента
     * @param int $studentId
     * @return int|null
     */
    public function fetch(int $studentId): ?int
    {
        $id = (new Query())
            ->select('cb.id')
            ->from(['cbs' => 'edu_class_book_student'])
            ->innerJoin(['cb' => 'edu_class_book'], 'cbs.class_book_id = cb.id')
            ->where(['cbs.student_id' => $studentId])
            ->scalar();
        return empty($id) ? null : (int) $id;
    }
}
