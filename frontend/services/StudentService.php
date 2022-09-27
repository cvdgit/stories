<?php

namespace frontend\services;

use common\models\User;
use common\models\UserStudent;
use DomainException;
use frontend\components\ModelDomainException;
use frontend\models\UserStudentForm;
use Yii;
use yii\db\StaleObjectException;

class StudentService
{

    public function create(int $userId, UserStudentForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $studentModel = UserStudent::createStudent($userId, $form->name, $form->class_id, $form->birth_date);
        if (!$studentModel->save()) {
            throw ModelDomainException::create($studentModel);
        }
    }

    public function update(UserStudent $studentModel, UserStudentForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $studentModel->updateStudent($form->name, $form->class_id, $form->birth_date);
        if (!$studentModel->save()) {
            throw ModelDomainException::create($studentModel);
        }
    }

    public function isOwnerThisUser(UserStudent $studentModel, int $userId): bool
    {
        return $studentModel->userOwned($userId);
    }

    /**
     * @throws StaleObjectException
     */
    public function delete(int $studentId, int $userId): void
    {
        if (($studentModel = UserStudent::findOne($studentId)) === null) {
            throw new DomainException('Ученик не найден');
        }

        if (!$this->isOwnerThisUser($studentModel, $userId)) {
            throw new DomainException('Отказано в доступе');
        }

        $studentModel->delete();
    }
}
