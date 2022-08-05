<?php

declare(strict_types=1);


namespace modules\edu\services;

use common\components\ModelDomainException;
use DomainException;
use modules\edu\forms\teacher\ClassBookForm;
use modules\edu\models\EduClassBook;

class TeacherService
{

    public function createClassBook(int $userId, ClassBookForm $form): int
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $model = EduClassBook::create($form->name, $userId, (int)$form->class_id);
        $model->addPrograms($form->programs);
        if (!$model->save()) {
            throw ModelDomainException::create($model);
        }
        return $model->id;
    }

    public function addStudentToClassBook(int $classBookId, int $studentId): void
    {
        if (($classBook = EduClassBook::findOne($classBookId)) === null) {
            throw new DomainException('Класс не найден');
        }
        $classBook->addStudent($studentId);
        if (!$classBook->save()) {
            throw ModelDomainException::create($classBook);
        }
    }
}
