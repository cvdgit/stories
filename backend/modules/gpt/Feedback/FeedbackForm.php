<?php

declare(strict_types=1);

namespace backend\modules\gpt\Feedback;

use common\models\User;
use yii\base\Model;

class FeedbackForm extends Model
{
    public $user_id;

    public function rules(): array
    {
        return [
            ['user_id', 'integer'],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
        ];
    }
}
