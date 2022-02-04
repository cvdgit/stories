<?php

namespace api\modules\v1\models;

use common\models\story\StoryStatus;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StorySearch extends Model
{

    public $title;
    public $category_id;
    public $story_list_id;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['title', 'safe'],
            [['category_id', 'story_list_id'], 'integer'],
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Story::find();
        $query->joinWith(['categories']);
        $query->andWhere(['{{%story}}.status' => StoryStatus::PUBLISHED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['published_at' => SORT_DESC],
            ],
            'pagination' => false,
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'title', $this->title]);

        if (!empty($this->story_list_id)) {
            if (($listModel = StoryList::findOne($this->story_list_id)) !== null) {
                $query->andFilterWhere(['in', 'category.id', $listModel->getCategoryIds()]);
            }
            else {
                $query->andWhere('1 = 0');
            }
        }

        if (!empty($this->category_id)) {
            $query->andFilterWhere(['in', 'category.id', explode(',', $this->category_id)]);
        }

        return $dataProvider;
    }

}