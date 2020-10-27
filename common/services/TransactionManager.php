<?php

namespace common\services;

use Exception;

class TransactionManager
{

    /**
     * @param callable $function
     * @throws Exception
     */
    public function wrap(callable $function): void
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $function();
            $transaction->commit();
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

}