<?php

namespace backend\models\test\import;

use backend\components\import\WordListAdapter;
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
    public $number_answers;

    public function rules()
    {
        return [
            [['test_id', 'word_list_id', 'number_answers'], 'required'],
            [['test_id', 'word_list_id'], 'integer'],
            ['number_answers', 'integer', 'max' => 10, 'min' => 2],
            ['test_id', 'exist', 'targetClass' => StoryTest::class, 'targetAttribute' => ['test_id' => 'id']],
            ['word_list_id', 'exist', 'targetClass' => TestWordList::class, 'targetAttribute' => ['word_list_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'word_list_id' => 'Список слов',
            'test_id' => 'Тест',
            'number_answers' => 'Количество ответов',
        ];
    }

    public function import(): void {

        if (!$this->validate()) {
            throw new DomainException('Model not valid');
        }

        $wordList = TestWordList::find()
            ->where('id = :id', [':id' => $this->word_list_id])
            ->with('testWords')
            ->orderBy(['name' => SORT_ASC])
            ->one();

        $wordListAdapter = new WordListAdapter($wordList);
        $questions = $wordListAdapter->create($this->number_answers);

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