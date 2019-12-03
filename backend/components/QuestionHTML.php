<?php


namespace backend\components;


use common\models\StoryTestAnswer;
use common\models\StoryTestQuestion;
use yii\helpers\Html;

class QuestionHTML
{

    /** @var StoryTestQuestion */
    protected $question;

    public function __construct($question)
    {
        $this->question = $question;
    }

    public function loadHTML(): string
    {
        $content = $this->createQuestions();
        $content .= $this->createControls();
        $content .= $this->createResults();
        return Html::tag('div', $content, ['class' => 'wikids-test']);
    }

    protected function createQuestions(): string
    {
        $content = $this->createQuestion();
        return Html::tag('div', $content, ['class' => 'wikids-test-questions']);
    }

    protected function createQuestion(): string
    {
        $content = Html::tag('p', $this->question->name);
        $content .= $this->createAnswers($this->question->storyTestAnswers);
        return Html::tag('div', $content, [
            'class' => 'wikids-test-question',
            'data-question-id' => $this->question->id,
        ]);
    }

    protected function createAnswers($answers): string
    {
        $content = '';
        foreach ($answers as $answer) {
            $content .= $this->createAnswer($answer);
        }
        return Html::tag('div', $content, ['class' => 'wikids-test-answers', 'data-mix-answers' => $this->question->mix_answers]);
    }

    protected function createAnswer(StoryTestAnswer $answer): string
    {
        $inputType = 'radio';
        if ($this->question->type === StoryTestQuestion::QUESTION_TYPE_CHECKBOX) {
            $inputType = 'checkbox';
        }
        $inputName = 'answer' . $answer->id;
        $contentInner = Html::input($inputType, $inputName, $answer->id, ['id' => $inputName]);
        if ($answer->image !== null) {
            $contentInner .= Html::img('/test_images/' . $answer->image, ['width' => 110]);
        }
        $contentInner .= Html::label($answer->name, $inputName);
        return Html::tag('div', $contentInner, ['class' => 'wikids-test-answer']);
    }

    protected function createControls(): string
    {
        $buttons = $this->createButtons();
        $controlsInner = Html::tag('div', $buttons, ['class' => 'wikids-test-buttons']);
        return Html::tag('div', $controlsInner, ['class' => 'wikids-test-controls']);
    }

    protected function createButtons(): string
    {
        $buttons = Html::button('Ответить на вопрос', ['data-answer-question' => $this->question->id]);
        return $buttons;
    }

    protected function createResults(): string
    {
        $content = Html::tag('p', '');
        $content .= Html::button('Перейти к следующему слайду');
        return Html::tag('div', $content, ['class' => 'wikids-test-results', 'style' => 'display: none']);
    }

}