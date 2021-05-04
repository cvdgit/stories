<?php

namespace backend\models\test\import;

use backend\components\import\WordListAdapter;
use backend\models\question\QuestionType;
use common\models\StoryTest;
use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use common\models\TestWordList;
use DomainException;
use yii\base\Model;

class ImportFromWordList extends Model
{

    public $test_id;
    public $word_list_id;

    public function rules()
    {
        return [
            [['test_id', 'word_list_id'], 'required'],
            [['test_id', 'word_list_id'], 'integer'],
            ['test_id', 'exist', 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
            ['word_list_id', 'exist', 'targetClass' => TestWordList::class, 'targetAttribute' => ['word_list_id' => 'id']],
        ];
    }

    public function import() {
        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $wordList = TestWordList::find()
            ->where('id = :id', [':id' => $this->word_list_id])
            ->with('testWords')
            ->one();

        $wordListAdapter = new WordListAdapter($wordList);
        $questions = $wordListAdapter->create();

        foreach ($questions as $question) {
            $model = StoryTestQuestion::create($this->test_id, $question['name'], $question['type']);
            $questionAnswers = [];
            foreach ($question['answers'] as $answer) {
                $questionAnswers[] = StoryTestAnswer::createFromRelation($answer['name'], $answer['correct']);
            }
            $model->storyTestAnswers = $questionAnswers;
            $model->save();
        }
    }

}