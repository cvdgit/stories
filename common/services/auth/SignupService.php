<?php


namespace common\services\auth;

use Exception;
use frontend\components\queue\UnisenderAddJob;
use RuntimeException;
use Yii;
use common\models\User;
use common\services\TransactionManager;

class SignupService
{

    protected $transaction;

    public function __construct(TransactionManager $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param $username
     * @param $email
     * @param $password
     * @throws Exception
     */
    public function signup($username, $email, $password): void
    {
        $user = User::createSignup(
            $username,
            $email,
            $password
        );
        $this->transaction->wrap(function () use ($user) {

            /* @var $user User */
            $user->save();

            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('user');
            $auth->assign($authorRole, $user->getId());
        });
    }

    public function sentEmailConfirm(User $user): void
    {
        $sent = Yii::$app->mailer
            ->compose(['html' => 'userSignupComfirm-html', 'text' => 'userSignupComfirm-text'], ['user' => $user])
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
            ->setSubject('Подтверждение регистрации')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Confirm email sent error');
        }
    }

    public function sendWelcomeEmail(User $user)
    {
        $sent = Yii::$app->mailer
            ->compose(['html' => 'userWelcome-html', 'text' => 'userWelcome-text'], ['user' => $user])
            ->setTo($user->email)
            ->setFrom([Yii::$app->params['infoEmail'] => Yii::$app->name])
            ->setSubject('Добро пожаловать на Wikids')
            ->send();
        if (!$sent) {
            throw new RuntimeException('Welcome email sent error');
        }
    }

    public function addJob(int $userID)
    {
        Yii::$app->queue->push(new UnisenderAddJob([
            'userID' => $userID,
        ]));
    }

}