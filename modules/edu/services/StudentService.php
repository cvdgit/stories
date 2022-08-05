<?php

declare(strict_types=1);


namespace modules\edu\services;

use common\components\ModelDomainException;
use common\models\UserStudent;
use common\services\TransactionManager;
use modules\edu\components\StudentLoginGenerator;
use modules\edu\forms\teacher\StudentForm;
use modules\edu\models\EduClassBook;
use modules\edu\models\StudentLogin;

class StudentService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function createStudent(int $userId, EduClassBook $classBook, StudentForm $form): int
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $studentModel = UserStudent::createStudent($userId, $form->name, $classBook->class_id);

        $this->transactionManager->wrap(function() use ($studentModel) {

            if (!$studentModel->save()) {
                throw ModelDomainException::create($studentModel);
            }

            $this->createStudentLogin($studentModel->id, StudentLoginGenerator::generateLogin(), StudentLoginGenerator::generatePassword());
        });

        return $studentModel->id;
    }

    public function createStudentLogin(int $studentId, string $username, string $password)
    {
        $studentLogin = StudentLogin::create($studentId, $username, $password);
        if (!$studentLogin->save()) {
            throw ModelDomainException::create($studentLogin);
        }
    }
}
