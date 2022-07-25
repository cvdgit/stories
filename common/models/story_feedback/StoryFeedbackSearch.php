<?php

namespace common\models\story_feedback;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use common\models\story_feedback\StoryFeedback;

/**
 * StoryFeedbackSearch represents the model behind the search form of `common\models\StoryFeedback`.
 */
class StoryFeedbackSearch extends Model
{

    public $status;

    public function rules(): array
    {
        return [
            [['status'], 'required'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => StoryFeedbackStatus::all()],
        ];
    }

    public function search(int $status, array $params): DataProviderInterface
    {
        $query = StoryFeedback::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);

        $this->load($params);
        $this->status = $status;
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->where(['status' => $this->status]);

        return $dataProvider;
    }
}
