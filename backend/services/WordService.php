<?php

declare(strict_types=1);

namespace backend\services;

use backend\components\WordListFormatter;
use backend\forms\WordForm;
use common\models\TestWord;
use Yii;

class WordService
{
    /** @var WordListFormatter */
    private $wordFormatter;

    public function __construct(WordListFormatter $wordFormatter)
    {
        $this->wordFormatter = $wordFormatter;
    }

    public function create(int $wordListId, WordForm $form): void
    {
        $word = $this->wordFormatter->createOne($form->name, $form->correct_answer);
        Yii::$app->db->createCommand()
            ->insert('test_word', [
                'name' => $word['name'],
                'word_list_id' => $wordListId,
                'order' => 1,
                'correct_answer' => $word['correct_answer'],
            ])
            ->execute();
    }

    public function update(int $wordId, WordForm $form): void
    {
        $word = $this->wordFormatter->createOne($form->name, $form->correct_answer);
        Yii::$app->db->createCommand()
            ->update('test_word', [
                'name' => $word['name'],
                'correct_answer' => $word['correct_answer'],
            ], ['id' => $wordId])
            ->execute();
    }

    public function delete(int $wordId): void
    {
        Yii::$app->db->createCommand()
            ->delete('test_word', ['id' => $wordId])
            ->execute();
    }

    public function copy(int $wordListId, WordForm $form): void
    {
        $word = $this->wordFormatter->createOne($form->name, $form->correct_answer);
        Yii::$app->db->createCommand()
            ->insert('test_word', [
                'name' => $word['name'],
                'word_list_id' => $wordListId,
                'order' => 1,
                'correct_answer' => $word['correct_answer'],
            ])
            ->execute();
    }
}
