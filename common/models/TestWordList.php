<?php

namespace common\models;

use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "test_word_list".
 *
 * @property int $id
 * @property string $name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property TestWord[] $testWords
 * @property Story[] $stories
 */
class TestWordList extends ActiveRecord
{
    public $linked_story;

    public static function tableName(): string
    {
        return 'test_word_list';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'stories',
                ],
            ],
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Заголовок',
            'created_at' => 'Создан',
            'updated_at' => 'Изменен',
        ];
    }

    public function getTestWords(): ActiveQuery
    {
        return $this->hasMany(TestWord::class, ['word_list_id' => 'id'])
            ->orderBy(['order' => SORT_ASC]);
    }

    public function getTestWordsCount()
    {
        return $this->getTestWords()->count();
    }

    private function getWordsQuery($filter = null)
    {
        $query = $this->getTestWords();
        if ($filter !== null) {
            $ids = array_map(static function($item) {
                return $item['entity_id'];
            }, $filter);
            $query->andFilterWhere(['not in', 'id', $ids]);
        }
        return $query;
    }

    public function getTestWordsAsArray($filter = null): array
    {
        return $this->getWordsQuery($filter)->asArray()->all();
    }

    public function getTestWordsData(int $testID, int $studentID, $filter = null)
    {
        $data = $this->getWordsQuery($filter)->all();

        $rememberData = TestRememberAnswer::getTestRememberAnswerData($testID, $studentID);
        foreach ($rememberData as $row) {
            foreach ($data as $key => $value) {
                if ((int)$value['id'] === (int)$row['entity_id']) {
                    $data[$key]['correct_answer'] = $row['answer'];
                    break;
                }
            }
        }

        return $data;
    }

    public static function getWordListAsArray()
    {
        return ArrayHelper::map(self::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
    }

    public static function create(string $name, int $storyId = null): TestWordList
    {
        $model = new self();
        $model->name = $name;
        if ($storyId !== null) {
            $model->stories = [$storyId];
        }
        return $model;
    }

    public function updateWordList(string $name, int $storyId = null): void
    {
        $this->name = $name;
        $this->stories = $storyId === null ? null : [$storyId];
    }

    public function getStories(): ActiveQuery
    {
        return $this->hasMany(Story::class, ['id' => 'story_id'])
            ->viaTable('test_word_list_story', ['test_word_list_id' => 'id']);
    }

    public function getLinkedStories()
    {
        return array_map(function(Story $story) {
            return [
                'title' => $story->title,
                'url' => $story->getStoryUrl(),
                'image' => $story->getListThumbPath(),
            ];
        }, $this->stories);
    }

    public static function getUpdateUrl(int $id)
    {
        return Url::to(['word-list/update', 'id' => $id]);
    }

}
