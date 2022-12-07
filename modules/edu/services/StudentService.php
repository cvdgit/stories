<?php

declare(strict_types=1);

namespace modules\edu\services;

use common\components\ModelDomainException;
use common\models\User;
use common\models\UserStudent;
use common\services\TransactionManager;
use DomainException;
use Exception;
use modules\edu\components\StudentLoginGenerator;
use modules\edu\forms\student\StudentForm;
use modules\edu\models\EduParentInvite;
use modules\edu\models\EduParentStudent;
use modules\edu\models\EduStudent;
use modules\edu\models\StudentLogin;
use Yii;

class StudentService
{
    private $transactionManager;
    private $userService;
    private $teacherService;
    private $userAccessService;

    public function __construct(
        TransactionManager $transactionManager,
        UserService $userService,
        TeacherService $teacherService,
        UserAccessService $userAccessService
    ) {
        $this->transactionManager = $transactionManager;
        $this->userService = $userService;
        $this->teacherService = $teacherService;
        $this->userAccessService = $userAccessService;
    }

    /**
     * @throws Exception
     */
    public function createStudentByParent(int $parentId, string $username, StudentForm $form, string $studentLogin, string $studentPassword): void
    {

        $this->transactionManager->wrap(function() use ($parentId, $username, $form, $studentLogin, $studentPassword) {

            $this->userService->createUserForStudent($username, $form->name, $form->name);
            if ((!$user = User::findByUsername($username)) === null) {
                throw new DomainException('Пользователь не найден');
            }

            $studentModel = EduStudent::createByParent($user->id, $form->name, (int)$form->class_id);
            if (!$studentModel->save()) {
                throw ModelDomainException::create($studentModel);
            }

            $this->createStudentLogin($studentModel->id, $studentLogin, $studentPassword);
            $this->createParentStudent($parentId, $studentModel->id);
        });
    }

    /**
     * @throws Exception
     */
    public function updateStudent(UserStudent $student, StudentForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $student->updateStudent($form->name, (int)$form->class_id);
        if (!$student->save()) {
            throw ModelDomainException::create($student);
        }
    }

    public function createStudentLogin(int $studentId, string $username, string $password): void
    {
        $studentLogin = StudentLogin::create($studentId, $username, $password);
        if (!$studentLogin->save()) {
            throw ModelDomainException::create($studentLogin);
        }
    }

    public function createStudentModel(int $userId, StudentForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }
        $studentModel = UserStudent::createStudent($userId, $form->name, (int)$form->class_id);
        if (!$studentModel->save()) {
            throw ModelDomainException::create($studentModel);
        }
    }

    /**
     * @throws Exception
     */
    public function createStudentWithUserAndAddToClassBook(string $username, StudentForm $studentForm, int $classBookId): void
    {

        $this->transactionManager->wrap(function() use ($username, $studentForm, $classBookId) {

            $this->userService->createUserForStudent($username, $studentForm->name, $studentForm->name);
            if ((!$user = User::findByUsername($username)) === null) {
                throw new DomainException('Пользователь не найден');
            }

            $this->createStudentModel($user->id, $studentForm);
            $user->refresh();

            if (count($user->students) === 0) {
                throw new DomainException('Ученик не найден');
            }
            $student = $user->students[0];

            $this->createStudentLogin($student->id, StudentLoginGenerator::generateLogin(), StudentLoginGenerator::generatePassword());

            $this->teacherService->addStudentToClassBook($classBookId, $student->id);

            $this->userAccessService->createAccess($user->id);
        });
    }

    public function changeUser(int $studentId, int $userId): void
    {
        Yii::$app->db->createCommand()
            ->update('user_student', ['user_id' => $userId], ['id' => $studentId])
            ->execute();
    }

    public function changeStatus(int $studentId, int $status): void
    {
        Yii::$app->db->createCommand()
            ->update('user_student', ['status' => $status], ['id' => $studentId])
            ->execute();
    }

    public function delete(int $studentId): void
    {
        $this->transactionManager->wrap(static function() use ($studentId) {
            Yii::$app->db->createCommand()
                ->delete('user_student', ['id' => $studentId])
                ->execute();
            Yii::$app->db->createCommand()
                ->delete('user_student_session', ['student_id' => $studentId])
                ->execute();
        });
    }

    private function createParentStudent(int $parentId, int $studentId): void
    {
        $parentStudent = EduParentStudent::create($parentId, $studentId);
        if (!$parentStudent->save()) {
            throw new DomainException('Произошла ошибка');
        }
    }

    public function setStudentParent(int $parentId, EduParentInvite $invite): void
    {
        if ($invite->isActive()) {
            throw new DomainException('Приглашение уже использовано');
        }

        $invite->setInviteActive();

        $this->transactionManager->wrap(function() use($parentId, $invite) {

            $this->createParentStudent($parentId, $invite->student_id);

            if (!$invite->save()) {
                throw new DomainException('Произошла ошибка');
            }

            //$this->changeUser($invite->student_id, $parentId);
            //$this->changeStatus($invite->student_id, UserStudent::STATUS_STUDENT);

            $this->userAccessService->createAccess($parentId);
        });
    }
}
