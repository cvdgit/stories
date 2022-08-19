<?php

declare(strict_types=1);

namespace modules\edu\services;

use common\components\ModelDomainException;
use common\services\TransactionManager;
use DomainException;
use modules\edu\forms\admin\ClassProgramTopicOrderForm;
use Yii;

class ClassProgramService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function saveOrder(int $classProgramId, ClassProgramTopicOrderForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $topicIds = $form->topic_ids;
        if (count($topicIds) === 0) {
            throw new DomainException('Список тем пуст');
        }

        $this->transactionManager->wrap(function() use ($classProgramId, $topicIds) {

            $db = Yii::$app->db;

            foreach ($topicIds as $order => $topicId) {

                $db->createCommand()
                    ->update(
                        'edu_topic',
                        ['order' => ++$order],
                        'class_program_id = :class AND id = :id',
                        [':class' => $classProgramId, ':id' => $topicId]
                    )
                    ->execute();
            }
        });
    }
}
