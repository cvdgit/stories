<?php

namespace modules\edu\services;

use common\components\ModelDomainException;
use common\services\TransactionManager;
use DomainException;
use modules\edu\forms\admin\LessonStoryOrderForm;
use modules\edu\forms\admin\SelectStoryForm;
use modules\edu\models\EduLesson;
use Yii;

class LessonService
{

    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function addStory(EduLesson $lessonModel, SelectStoryForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $lessonModel->addStory($form->story_id);
        if (!$lessonModel->save()) {
            throw ModelDomainException::create($lessonModel);
        }
    }

    public function saveOrder(int $lessonId, LessonStoryOrderForm $form): void
    {
        if (!$form->validate()) {
            throw ModelDomainException::create($form);
        }

        $storyIds = $form->story_ids;
        if (count($storyIds) === 0) {
            throw new DomainException('Список историй пуст');
        }

        $this->transactionManager->wrap(function() use ($lessonId, $storyIds) {

            $db = Yii::$app->db;

            foreach ($storyIds as $order => $storyId) {

                $db->createCommand()
                    ->update(
                        'edu_lesson_story',
                        ['order' => ++$order],
                        'lesson_id = :lesson AND story_id = :story',
                        [':lesson' => $lessonId, ':story' => $storyId]
                    )
                    ->execute();
            }
        });
    }

    public function deleteStory(int $lessonId, int $storyId): void
    {
        Yii::$app->db->createCommand()
            ->delete('edu_lesson_story', ['lesson_id' => $lessonId, 'story_id' => $storyId])
            ->execute();
    }

    public function delete(int $lessonId): void
    {
        if (($lessonModel = EduLesson::findOne($lessonId)) === null) {
            throw new DomainException('Урок не найден');
        }
        $lessonModel->delete();
    }
}
