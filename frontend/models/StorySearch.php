<?php


namespace frontend\models;


use common\models\Story;
use frontend\components\StorySorter as Sort;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StorySearch extends Model
{

    public $title;
    public $description;
    public $category_id;
    public $tag_id;
    public $audio;

    public $defaultSortField;
    public $defaultSortOrder;

    public function rules()
    {
        return [
            [['title', 'description'], 'string'],
            [['tag_id', 'audio'], 'integer'],
            ['category_id', 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Story::findPublishedStories();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 40,
            ],
        ]);

        $defaultOrder = ['created_at' => SORT_DESC];
        if ($this->defaultSortField !== null) {
            $defaultOrder = [$this->defaultSortField => $this->defaultSortOrder];
        }
        $sortParams = [
            'defaultOrder' => $defaultOrder,
            'attributes' => [
                'created_at' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => 'дате создания',
                ],
                'title' => [
                    'asc' => ['title' => SORT_ASC],
                    'desc' => ['title' => SORT_DESC],
                    'label' => 'названию истории',
                ],
                'episode' => [
                    'asc' => ['episode' => SORT_ASC],
                    'desc' => ['episode' => SORT_DESC],
                    'label' => 'эпизодам',
                ],
            ],
        ];
        $sort = new Sort($sortParams);
        $dataProvider->setSort($sort);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->category_id)) {
            $query->joinWith(['categories']);
        }

        if (!empty($this->tag_id)) {
            $query->joinWith(['tags']);
        }

        $query->andFilterWhere(['or',
            ['like', '{{%story}}.title', $this->title],
            ['like', '{{%story}}.description', $this->title],
        ]);
        $query->andFilterWhere(['tag.id' => $this->tag_id]);
        $query->andFilterWhere(['in', 'category.id', $this->category_id]);
        $query->andFilterWhere(['audio' => $this->audio]);

        return $dataProvider;
    }

}