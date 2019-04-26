<?php


namespace common\services;

use common\models\User;
use yii\web\NotFoundHttpException;
use common\models\PaymentQuery;

class UserService
{

    /**
     * @param $id
     * @return User|null
     * @throws NotFoundHttpException
     */
    public function findUserByID($id): ?User
    {
        if (($user = User::findOne($id)) !== null) {
            return $user;
        }
        throw new NotFoundHttpException('Пользователь не найден.');
    }

    /**
     * @param $userID
     * @return bool
     * @throws NotFoundHttpException
     */
    public function hasSubscription($userID): bool
    {
        /* @var $user User */
        $user = $this->findUserByID($userID);
        return $user->hasSubscription();
    }

    /**
     * @param $userID
     * @return bool
     * @throws NotFoundHttpException
     */
    public function hasFreeSubscription($userID): bool
    {
        /* @var $user User */
        $user = $this->findUserByID($userID);
        /* @var $payments PaymentQuery */
        $payments = $user->getPayments();
        return $payments->freeSubscription()->exists();
    }

    /**
     * @param $userID
     * @return bool
     * @throws NotFoundHttpException
     */
    public function hasValidFreeSubscription($userID): bool
    {
        /* @var $user User */
        $user = $this->findUserByID($userID);
        /* @var $payments PaymentQuery */
        $payments = $user->getPayments();
        return $payments->freeSubscription()->isValid()->exists();
    }

}