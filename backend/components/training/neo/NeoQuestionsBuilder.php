<?php

namespace backend\components\training\neo;

use backend\components\training\base\UserProgress;

class NeoQuestionsBuilder
{

    private $data;
    private $userProgress;
    private $repeat;

    public function __construct(array $data, UserProgress $userProgress, int $repeat)
    {
        $this->data = $data;
        $this->userProgress = $userProgress;
        $this->repeat = $repeat;
    }

    private function buildAnswers(array $data): array
    {
        $answers = [];
        foreach ($data as $answerItem) {
            $answer = [
                'id' => $answerItem['id'],
                'name' => $answerItem['answer'],
                'is_correct' => $answerItem['correct'] ? 1 : 0,
                'image' => $answerItem['image'],
                'description' => $answerItem['description'] ?? '',
            ];
            $answers[] = $answer;
        }
        return $answers;
    }

    public function build(): array
    {
        $questions = [];
        foreach ($this->data as $questionItem) {

            $questionID = (int)$questionItem['hash'];

            $skipQuestion = false;
            foreach ($this->userProgress->getHistory() as $history) {
                if ((int)$history['entity_id'] === $questionID) {
                    $skipQuestion = true;
                    break;
                }
            }
            if ($skipQuestion) {
                continue;
            }

            $stars = 0;
            foreach ($this->userProgress->getStars() as $star) {
                if ((int)$star['entity_id'] === $questionID) {
                    $stars = $star['stars'];
                    break;
                }
            }

            $svg = $questionItem['question_svg'] ?? false;

            $question = [
                'id' => $questionID,
                'name' => $questionItem['question'],
                'mix_answers' => 0,
                'type' => ((int)$questionItem['correct_number'] > 1 ? 1 : 0),
                'image' => $questionItem['question_image'],
                'images' => $questionItem['question_images'],
                'storyTestAnswers' => $this->buildAnswers($questionItem['answers']),
                'entity_id' => $questionItem['question_entity_id'],
                'entity_name' => $questionItem['question_entity_name'],
                'relation_id' => $questionItem['question_relation_id'],
                'relation_name' => $questionItem['question_relation_name'],
                'topic_id' => $questionItem['question_topic_id'],
                'topic_name' => $questionItem['question_topic_name'],
                'correct_number' => $questionItem['correct_number'],
                'stars' => [
                    'total' => $this->repeat,
                    'current' => (int)$stars,
                ],
                'view' => $svg ? 'svg' : '',
                'svg' => $svg,
                'lastAnswerIsCorrect' => true,
                'answer_number' => $questionItem['answer_number'],
                'params' => $questionItem['params'] ?? [],
            ];
            $questions[] = $question;
        }
        return $questions;
    }
}
