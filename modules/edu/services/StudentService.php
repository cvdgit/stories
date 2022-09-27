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
use modules\edu\models\StudentLogin;
use Yii;

class StudentService
{

    private $transactionManager;
    private $userService;
    private $teacherService;
    private $userAccessService;

    public function __construct(TransactionManager $transactionManager,
                                UserService $userService,
                                TeacherService $teacherService,
                                UserAccessService $userAccessService)
    {
        $this->transactionManager = $transactionManager;
        $this->userService = $userService;
        $this->teacherService = $teacherService;
        $this->userAccessService = $userAccessService;
    }

    /**
     * @throws Exception
     */
    public function createStudent(int $userId, StudentForm $form): int
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $studentModel = UserStudent::createStudent($userId, $form->name, (int)$form->class_id);

        $this->transactionManager->wrap(function() use ($studentModel) {

            if (!$studentModel->save()) {
                throw ModelDomainException::create($studentModel);
            }

            $this->createStudentLogin($studentModel->id, StudentLoginGenerator::generateLogin(), StudentLoginGenerator::generatePassword());
        });

        return $studentModel->id;
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
}
