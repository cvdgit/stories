<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use common\models\StoryStatistics;

/**
 * StoryStatisticsSearch represents the model behind the search form of `common\models\StoryStatistics`.
 */
class StoryStatisticsSearch extends StoryStatistics
{

    public function rules()
    {
        return [
            [['slide_number', 'slide_time', 'chars'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($story_id, $params)
    {
        $query = StoryStatistics::findStoryStatistics($story_id);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        
        $sort = new Sort([
            'defaultOrder' => ['created_at' => SORT_DESC],
        ]);
        $dataProvider->setSort($sort);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function getChartData($story_id)
    {
        $query = new \yii\db\Query();
        $rows = $query->select(['slide_number', 'COUNT(id) AS views'])
                      ->from('story_statistics')
                      ->where('story_id = :storyid', [':storyid' => $story_id])
                      ->groupBy('slide_number')
                      ->indexBy('slide_number')
                      ->all();
        return [
            'labels' => array_keys($rows),
            'data' => array_map(function($elem) { return $elem['views']; }, $rows),
        ];
    }

    public function getChartData2($story_id)
    {
        $query = new \yii\db\Query();
        $rows = $query->select(['slide_number', 'ROUND(AVG(end_time - begin_time)) AS time'])
                      ->from('story_statistics')
                      ->where('story_id = :storyid', [':storyid' => $story_id])
                      ->groupBy('slide_number')
                      ->indexBy('slide_number')
                      ->all();
        return [
            'labels' => array_keys($rows),
            'data' => array_map(function($elem) { return $elem['time']; }, $rows),
        ];
    }

    public function getChartData3($story_id)
    {
        $query = new \yii\db\Query();
        $rows = $query->select(['slide_number', 'ROUND(AVG(end_time - begin_time) / MAX(chars), 1) AS time'])
                      ->from('story_statistics')
                      ->where('story_id = :storyid', [':storyid' => $story_id])
                      ->groupBy('slide_number')
                      ->indexBy('slide_number')
                      ->all();
        return [
            'labels' => array_keys($rows),
            'data' => array_map(function($elem) { return $elem['time']; }, $rows),
        ];
    }

}
