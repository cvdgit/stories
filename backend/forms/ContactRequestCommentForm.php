<?php

declare(strict_types=1);

namespace backend\forms;

use yii\base\Model;

class ContactRequestCommentForm extends Model
{
    public $comment;

    public function rules(): array
    {
        return [
            ['comment', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'comment' => 'Комментарий',
        ];
    }
}
