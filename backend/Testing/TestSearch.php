<?php

declare(strict_types=1);

namespace backend\Testing;

use common\models\StoryTest;
use common\models\test\SourceType;
use common\models\test\TestStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class TestSearch extends Model
{
    public $title;
    public $created_at;
    public $source;
    public $answer_type;
    public $status;
    public $created_by;

    public $with_repetition;

    public function rules(): array
    {
        return [
            [['title'], 'string', 'max' => 50],
            [['created_at'], 'date'],
            ['status', 'default', 'value' => TestStatus::DEFAULT],
            [['source', 'answer_type', 'status', 'created_by'], 'integer'],
            ['status', 'in', 'range' => TestStatus::all()],
            [['with_repetition'], 'boolean'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'with_repetition' => 'С расписанием повторений',
        ];
    }

    public function isNeoTest(): bool
    {
        return $this->source === SourceType::NEO;
    }

    public function isWordList(): bool
    {
        return $this->source === SourceType::LIST;
    }

    public function isTests(): bool
    {
        return $this->source === SourceType::TESTS;
    }

    public function isTemplate(): bool
    {
        return $this->status === TestStatus::TEMPLATE;
    }

    public function search(int $userId, array $params = []): DataProviderInterface
    {
        $query = StoryTest::find()->with(['storyTestQuestions', 'createdBy']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andWhere('1 = 0');
            return $dataProvider;
        }

        $query->where('parent_id = 0');
        $query->andFilterWhere(['status' => $this->status]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere([
            "DATE_FORMAT(FROM_UNIXTIME(created_at), '%d.%m.%Y')" => $this->created_at,
        ]);
        $query->andFilterWhere(['source' => $this->source]);
        $query->andFilterWhere(['answer_type' => $this->answer_type]);

        if (!empty($this->created_by)) {
            $query->andFilterWhere(['created_by' => $this->created_by]);
        }

        if ($this->with_repetition) {
            $query->andWhere('schedule_id IS NOT NULL');
        }

        return $dataProvider;
    }
}
