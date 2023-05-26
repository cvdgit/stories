<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\ManageTopics;

use yii\base\Action;
use yii\db\Query;

class AdminManageTopicsFormAction extends Action
{
    public function run(int $class_book_id): string
    {
        $query = (new Query())
            ->select([
                'classProgramId' => 'cp.id',
                'programName' => 'p.name',
                'topicId' => 't.id',
                'topicName' => 't.name',
                'have_access' => 'acc.created_at',
            ])
            ->from(['cb' => 'edu_class_book'])
            ->innerJoin(['cbp' => 'edu_class_book_program'], 'cb.id = cbp.class_book_id')
            ->innerJoin(['cp' => 'edu_class_program'], 'cbp.class_program_id = cp.id')
            ->innerJoin(['p' => 'edu_program'], 'cp.program_id = p.id')
            ->innerJoin(['t' => 'edu_topic'], 't.class_program_id = cp.id')
            ->leftJoin(['acc' => 'edu_class_book_topic_access'], 't.id = acc.topic_id AND t.class_program_id = acc.class_program_id AND acc.class_book_id = cb.id')
            ->where(['cb.id' => $class_book_id])
            ->orderBy(['programName' => SORT_ASC]);

        $topics = $query->all();

        $accessForm = new ManageTopicForm([
            'class_book_id' => $class_book_id,
        ]);

        return $this->controller->renderAjax('_manage_topics', [
            'formModel' => $accessForm,
            'topicFormModel' => new TopicAccessForm(),
            'topics' => $topics,
        ]);
    }
}
