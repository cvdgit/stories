<?php

namespace backend\models\test_template;

use backend\services\WordListService;
use common\models\StoryTest;
use common\models\TestWordList;
use DomainException;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CreateTestsForm extends Model
{

    public $new_story;
    public $story_name;
    public $story_id;
    public $word_list_id;

    /** @var TestItemForm[] */
    public $items = [];

    private $wordListService;

    public function __construct($config = [])
    {
        $this->wordListService = Yii::createObject(WordListService::class);
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['new_story', 'boolean'],
            ['story_name', 'string', 'max' => 255],
            [['word_list_id', 'story_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'story_id' => 'Выбрать историю',
            'story_name' => 'Название новой истории',
            'new_story' => 'Новая история',
        ];
    }

    public static function getTestTemplateList(): array
    {
        return ArrayHelper::map(StoryTest::getTestTemplates(), 'id', 'title');
    }

    public static function getProcessingList(): array
    {
        return [
            0 => 'По умолчанию',
            1 => 'Обратный',
            2 => 'Только первый столбец',
            3 => 'Только второй столбец',
        ];
    }

    public function create(): void
    {
        if (!$this->validate()) {
            throw new DomainException('CreateTestsForm not valid');
        }
        if (empty($this->story_id) && empty($this->story_name)) {
            throw new DomainException('Не удалось определить историю');
        }
        if ($this->new_story === '0' && empty($this->story_id)) {
            throw new DomainException('Необходимо выбрать историю');
        }
        if ($this->new_story === '1' && empty($this->story_name)) {
            throw new DomainException('Необходимо указать название новой истории');
        }

        if (count($this->items) === 0) {
            throw new DomainException('Список шаблонов пуст');
        }

        $wordList = TestWordList::find()
            ->where('id = :id', [':id' => $this->word_list_id])
            ->with('testWords')
            ->orderBy(['name' => SORT_ASC])
            ->one();

        if ($this->new_story === '1') {
            $this->wordListService->createFromTemplate(Yii::$app->user->getId(), $this->story_name, $this->items, $wordList->testWords);
        }
        else {
            $this->wordListService->createFromTemplateExistsStory($this->story_id, $this->items, $wordList->testWords);
        }
    }
}