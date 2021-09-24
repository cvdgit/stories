<?php

namespace backend\models\test_template;

use backend\components\import\WordListModifierBuilder;
use backend\services\WordListService;
use common\models\StoryTest;
use common\models\TestWordList;
use DomainException;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class CreateTestsForm extends Model
{

    public $story_name;
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
            ['story_name', 'required'],
            ['story_name', 'string', 'max' => 255],
            ['word_list_id', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'story_name' => 'Название истории',
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

    public function create()
    {
        if (!$this->validate()) {
            throw new DomainException('CreateTestsForm not valid');
        }

        if (count($this->items) === 0) {
            throw new DomainException('Список шаблонов пуст');
        }

        $wordList = TestWordList::find()
            ->where('id = :id', [':id' => $this->word_list_id])
            ->with('testWords')
            ->orderBy(['name' => SORT_ASC])
            ->one();

        $this->wordListService->createFromTemplate(Yii::$app->user->getId(), $this->story_name, $this->items, $wordList->testWords);
    }
}