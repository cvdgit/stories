<?php

namespace backend\forms;

use common\models\Story;
use common\models\TestWordList;
use yii\base\Model;

class WordListForm extends Model
{
    /** @var string */
    public $name;
    /** @var string */
    public $story_id;

    private $id = null;
    /** @var Story|null */
    private $story = null;

    public function __construct(TestWordList $model = null, $config = [])
    {
        parent::__construct($config);
        if ($model !== null) {
            $this->id = $model->id;
            $this->name = $model->name;
            if (count($model->stories) > 0) {
                $this->story = $model->stories[0];
                $this->story_id = $model->stories[0]->id;
            }
        }
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['story_id'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Заголовок',
            'story_id' => 'История',
        ];
    }

    public function isNewRecord(): bool
    {
        return $this->id === null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }
}
