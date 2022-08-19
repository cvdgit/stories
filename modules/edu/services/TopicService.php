<?php

declare(strict_types=1);

namespace modules\edu\services;

use common\components\ModelDomainException;
use common\services\TransactionManager;
use DomainException;
use Exception;
use modules\edu\forms\admin\TopicLessonOrderForm;
use modules\edu\models\EduTopic;
use Yii;

class TopicService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @throws Exception
     */
    public function saveOrder(int $topicId, TopicLessonOrderForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $lessonIds = $form->lesson_ids;
        if (count($lessonIds) === 0) {
            throw new DomainException('Список уроков пуст');
        }

        $this->transactionManager->wrap(function() use ($topicId, $lessonIds) {

            $db = Yii::$app->db;

            foreach ($lessonIds as $order => $lessonId) {

                $db->createCommand()
                    ->update(
                        'edu_lesson',
                        ['order' => ++$order],
                        'topic_id = :topic AND id = :id',
                        [':topic' => $topicId, ':id' => $lessonId]
                    )
                    ->execute();
            }
        });
    }

    public function delete(int $topicId): void
    {
        if (($topicModel = EduTopic::findOne($topicId)) === null) {
            throw new DomainException('Тема не найдена');
        }
        $topicModel->delete();
    }
}
