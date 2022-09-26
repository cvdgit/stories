<?php

declare(strict_types=1);

namespace modules\edu\services;

use common\models\User;
use common\models\UserStudent;
use common\rbac\UserRoles;
use common\services\TransactionManager;
use DomainException;
use yii\db\Query;

class ClassBookService
{
    private $userService;
    private $studentService;
    private $transactionManager;
    private $userAccessService;

    public function __construct(
        UserService $userService,
        TransactionManager $transactionManager,
        StudentService $studentService,
        UserAccessService $userAccessService
    )
    {
        $this->userService = $userService;
        $this->transactionManager = $transactionManager;
        $this->studentService = $studentService;
        $this->userAccessService = $userAccessService;
    }

    private function isUserForStudentExists(int $studentId): bool
    {
        $query = (new Query())
            ->from('user_student')
            ->innerJoin('user', 'user_student.user_id = user.id')
            ->innerJoin('auth_assignment', 'user.id = auth_assignment.user_id')
            ->where(['user_student.id' => $studentId])
            ->andWhere(['auth_assignment.item_name' => UserRoles::ROLE_STUDENT]);
        return $query->exists();
    }

    public function createUserAndLinkToStudent(UserStudent $student): void
    {
        if ($this->isUserForStudentExists($student->id)) {
            throw new DomainException('Пользователь для ученика уже существует');
        }

        $this->transactionManager->wrap(function() use ($student) {

            $this->userService->createUserForStudent($username = User::createUsername(), $student->name, $student->name);
            if ((!$user = User::findByUsername($username)) === null) {
                throw new DomainException('Пользователь не найден');
            }

            $this->studentService->changeUser($student->id, $user->id);

            $this->userAccessService->createAccess($user->id);
        });
    }
}
