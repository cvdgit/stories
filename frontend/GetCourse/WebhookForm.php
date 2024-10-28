<?php

declare(strict_types=1);

namespace frontend\GetCourse;

use yii\base\Model;

class WebhookForm extends Model
{
    /** @var string */
    public $id;
    /** @var string */
    public $first_name;
    /** @var string */
    public $last_name;
    /** @var string */
    public $email;

    public function rules(): array
    {
        return [
            [['id', 'first_name', 'last_name', 'email'], 'required'],
            ['id', 'integer'],
            [['first_name', 'last_name'], 'string'],
            ['email', 'email'],
        ];
    }
}
