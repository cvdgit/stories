<?php

declare(strict_types=1);

namespace modules\testing\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 *
 * @property int $id
 * @property string $title [varchar(255)]
 * @property int $status [tinyint(3)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property int $mix_answers [tinyint(3)]
 * @property int $remote [tinyint(3)]
 * @property int $question_list_id [int(11)]
 * @property string $question_list_name [varchar(255)]
 * @property string $header [varchar(255)]
 * @property string $description_text
 * @property int $parent_id [int(11)]
 * @property string $question_params [varchar(255)]
 * @property string $incorrect_answer_text [varchar(255)]
 * @property int $source [tinyint(3)]
 * @property int $word_list_id [int(11)]
 * @property int $answer_type [tinyint(3)]
 * @property int $strict_answer [tinyint(3)]
 * @property string $wrong_answers_params [varchar(1000)]
 * @property string $input_voice [varchar(255)]
 * @property int $shuffle_word_list [tinyint(3)]
 * @property string $recording_lang [varchar(255)]
 * @property int $remember_answers [tinyint(3)]
 * @property int $ask_question [tinyint(3)]
 * @property string $ask_question_lang [varchar(255)]
 * @property int $created_by [int(11)]
 * @property int $hide_question_name [tinyint(3)]
 * @property int $answers_hints [tinyint(3)]
 * @property int $hide_answers_name [tinyint(3)]
 * @property int $repeat [tinyint(3)]
 * @property int $say_correct_answer [tinyint(3)]
 * @property int $voice_response [tinyint(3)]
 * @property int $show_descr_in_questions [tinyint(3)]
 *
 * @property Question[] $questions
 */
class Testing extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'story_test';
    }

    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(Question::class, ['story_test_id' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }
}
