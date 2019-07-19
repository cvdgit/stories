<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Sort;

use common\models\StoryStatistics;
use yii\db\Expression;
use yii\db\Query;


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
            'data' => array_values(array_map(function($elem) { return $elem['views']; }, $rows)),
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
            'data' => array_values(array_map(function($elem) { return $elem['time']; }, $rows)),
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
            'data' => array_values(array_map(function($elem) { return $elem['time']; }, $rows)),
        ];
    }

    public function getChartData4()
    {

        $subQuery = (new \yii\db\Query())->select(['ROUND(COUNT(stat.story_id) * 100 / stry.views_number)'])
                                         ->from('story_statistics stat')
                                         ->where('stat.story_id = stry.id AND stat.slide_number = stry.slides_number - 1');

        $rows = (new \yii\db\Query())->select(['stry.id', 'stry.title', 'stry.views_number', 'story_done' => $subQuery])
                                     ->from('story stry')
                                     ->where('stry.views_number > 0')
                                     ->orderBy(['stry.views_number' => SORT_DESC])
                                     ->limit(10)
                                     ->indexBy('id')
                                     ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $rows,
        ]);

        return $dataProvider;
    }

    public function chartStoryViews()
    {
        $data = (new Query())
            ->select(['DATE_FORMAT(FROM_UNIXTIME(`created_at`),\'%d-%m-%Y\') AS date', 'COUNT(DISTINCT `story_id`) AS views'])
            ->from('{{%story_statistics}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(CURDATE() - 12)'))
            ->andWhere(new Expression('`created_at` <= UNIX_TIMESTAMP(CURDATE() + 1)'))
            ->groupBy(new Expression('DATE_FORMAT(FROM_UNIXTIME(`created_at`),\'%d-%m-%Y\')'))
            ->indexBy('date')
            ->all();
        return [
            'labels' => array_keys($data),
            'data' => array_values(array_map(function($elem) { return $elem['views']; }, $data)),
        ];
    }

}
