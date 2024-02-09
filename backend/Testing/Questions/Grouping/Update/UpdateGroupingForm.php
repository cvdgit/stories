<?php

declare(strict_types=1);

namespace backend\Testing\Questions\Grouping\Update;

use common\models\StoryTestQuestion;
use yii\base\Model;

class UpdateGroupingForm extends Model
{
    public $name;
    public $payload;

    public function __construct(StoryTestQuestion $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->name = $model->name;
            $this->payload = $model->regions;
        }
    }

    public function rules(): array
    {
        return [
            [["name", "payload"], 'required'],
            ["payload", "safe"],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Вопрос',
        ];
    }
}
