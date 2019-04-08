<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\components\StorySorter as Sort;
use common\models\Story;

/**
 * StorySearch represents the model behind the search form of `common\models\Story`.
 */
class StorySearch extends Story
{

    const SCENARIO_FRONTEND = 'frontend';
    const SCENARIO_BACKEND = 'backend';

    public $tag_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'string'],
            [['user_id', 'category_id', 'tag_id', 'status', 'sub_access'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_FRONTEND => ['title', 'category_id', 'tag_id'],
            self::SCENARIO_BACKEND => ['title', 'user_id', 'category_id', 'tag_id', 'created_at', 'updated_at', 'status', 'sub_access'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $isBackend = ($this->scenario == StorySearch::SCENARIO_BACKEND);
        if ($isBackend) {
            $query = Story::findStories()->joinWith(['author', 'category', 'tags']);
        }
        else {
            $query = Story::findPublishedStories()->joinWith(['category', 'tags']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        $sortParams = [
            'defaultOrder' => ['sub_access' => SORT_DESC, 'title' => SORT_ASC],
            'attributes' => [
                'title' => [
                    'asc' => ['title' => SORT_ASC],
                    'desc' => ['title' => SORT_DESC],
                    'label' => 'названию истории',
                ],
                'user_id' => [
                    'asc' => ['user.username' => SORT_ASC],
                    'desc' => ['user.username' => SORT_DESC],
                ],
                'category_id' => [
                    'asc' => ['category.name' => SORT_ASC],
                    'desc' => ['category.name' => SORT_DESC],
                ],
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => 'дате создания',
                ],
                'updated_at' => [
                    'asc' => ['updated_at' => SORT_ASC],
                    'desc' => ['updated_at' => SORT_DESC],
                ],
                'sub_access',
                'views_number',
            ],
        ];
        if ($isBackend) {
            $sortParams['defaultOrder'] = ['created_at' => SORT_DESC];
        }
        $sort = new Sort($sortParams);
        $dataProvider->setSort($sort);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere([
            'user.id' => $this->user_id,
            'category.id' => $this->category_id,
            "DATE_FORMAT(FROM_UNIXTIME(story.created_at), '%d.%m.%Y')" => $this->created_at,
            "DATE_FORMAT(FROM_UNIXTIME(story.updated_at), '%d.%m.%Y')" => $this->updated_at,
            'story.status' => $this->status,
            'story.sub_access' => $this->sub_access,
            'tag.id' => $this->tag_id,
        ]);
        
        return $dataProvider;
    }
}
