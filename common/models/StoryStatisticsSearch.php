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
        $rows = $query->select(['{{%story_slide}}.number AS slide_number', 'COUNT({{%story_statistics}}.id) AS views'])
            ->from('{{%story_statistics}}')
            ->innerJoin('{{%story_slide}}', '{{%story_statistics}}.slide_id = {{%story_slide}}.id')
            ->where('{{%story_statistics}}.story_id = :story', [':story' => $story_id])
            ->groupBy('{{%story_slide}}.number')
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
        $rows = $query->select(['{{%story_slide}}.number AS slide_number', 'ROUND(AVG({{%story_statistics}}.end_time - {{%story_statistics}}.begin_time)) AS time'])
            ->from('{{%story_statistics}}')
            ->innerJoin('{{%story_slide}}', '{{%story_statistics}}.slide_id = {{%story_slide}}.id')
            ->where('{{%story_statistics}}.story_id = :story', [':story' => $story_id])
            ->groupBy('{{%story_slide}}.number')
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
        $rows = $query->select(['{{%story_slide}}.number AS slide_number', 'ROUND(AVG({{%story_statistics}}.end_time - {{%story_statistics}}.begin_time) / MAX({{%story_statistics}}.chars), 1) AS time'])
            ->from('{{%story_statistics}}')
            ->innerJoin('{{%story_slide}}', '{{%story_statistics}}.slide_id = {{%story_slide}}.id')
            ->where('{{%story_statistics}}.story_id = :story', [':story' => $story_id])
            ->groupBy('{{%story_slide}}.number')
            ->indexBy('slide_number')
            ->all();
        return [
            'labels' => array_keys($rows),
            'data' => array_values(array_map(function($elem) { return $elem['time']; }, $rows)),
        ];
    }

    public function getChartData4()
    {

        $doneSubQuery = (new Query())
            ->select(['ROUND(COUNT(DISTINCT {{%story_statistics}}.session) * 100 / t.storyViews, 2)'])
            ->from('{{%story_statistics}}')
            ->innerJoin('{{%story_slide}}', '{{%story_statistics}}.slide_id = {{%story_slide}}.id')
            ->where('{{%story_statistics}}.story_id = {{%story}}.id')
            ->andWhere('{{%story_slide}}.number = (SELECT MAX(z.number) - 1 FROM story_slide z WHERE z.story_id = {{%story_statistics}}.story_id AND z.status = 1)');

        $viewsSubQuery = (new Query())
            ->select(['{{%story_statistics}}.story_id AS storyID', 'COUNT(DISTINCT {{%story_statistics}}.session) AS storyViews'])
            ->from('{{%story_statistics}}')
            ->groupBy(['{{%story_statistics}}.story_id']);

        $rows = (new Query())
            ->select(['{{%story}}.id AS story_id', '{{%story}}.title', 't.storyViews AS views_number', 'story_done' => $doneSubQuery])
            ->from(['t' => $viewsSubQuery])
            ->innerJoin('{{%story}}', '{{%story}}.id = t.storyID')
            ->orderBy(['t.storyViews' => SORT_DESC])
            ->limit(10)
            ->indexBy('story_id')
            ->all();

        return new ArrayDataProvider([
            'allModels' => $rows,
        ]);
    }

    public function chartStoryViews()
    {
        $query = (new Query())
            ->select(['DATE_FORMAT(FROM_UNIXTIME(`created_at`),\'%d-%m-%Y\') AS stat_date', 'COUNT(DISTINCT `session`) AS views'])
            ->from('{{%story_statistics}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -10 DAY))'))
            ->andWhere(new Expression('`created_at` <= UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 1 DAY))'))
            ->groupBy(new Expression('DATE_FORMAT(FROM_UNIXTIME(`created_at`),\'%d-%m-%Y\')'));
        $query2 = (new Query())
            ->select(['DATE_FORMAT(FROM_UNIXTIME(`created_at`),\'%d-%m-%Y\') AS stat_date', 'COUNT(DISTINCT `story_id`) AS views'])
            ->from('{{%story_readonly_statistics}}')
            ->where(new Expression('`created_at` >= UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL -10 DAY))'))
            ->andWhere(new Expression('`created_at` <= UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 1 DAY))'))
            ->groupBy(new Expression('DATE_FORMAT(FROM_UNIXTIME(`created_at`),\'%d-%m-%Y\')'));
        $query->union($query2, true);
        $data = (new Query())
            ->select(['a.stat_date AS stat_date', 'SUM(a.views) AS views'])
            ->from(['a' => $query])
            ->groupBy('a.stat_date')
            ->orderBy(['stat_date' => SORT_ASC])
            ->indexBy('stat_date')
            ->all();

        uksort($data, function ($dt1, $dt2) {
            $tm1 = strtotime($dt1);
            $tm2 = strtotime($dt2);
            return ($tm1 < $tm2) ? -1 : (($tm1 > $tm2) ? 1 : 0);
        });

        return [
            'labels' => array_keys($data),
            'data' => array_values(array_map(function($elem) { return $elem['views']; }, $data)),
        ];
    }

    public function statDateFrom()
    {
        return (new Query())->from('{{%story_statistics}}')->min('created_at');
    }

    public function readOnlyData(): ArrayDataProvider
    {
        $models = (new Query())
            ->select(['{{%story}}.id', '{{%story}}.title', 'COUNT({{%story_readonly_statistics}}.story_id) AS views_number'])
            ->from('{{%story_readonly_statistics}}')
            ->innerJoin('{{%story}}', '{{%story}}.id = {{%story_readonly_statistics}}.story_id')
            ->groupBy('{{%story_readonly_statistics}}.story_id')
            ->orderBy(['views_number' => SORT_DESC])
            ->limit(10)
            ->indexBy('id')
            ->all();
        return new ArrayDataProvider([
            'allModels' => $models,
        ]);
    }

    public function readOnlyStatDateFrom()
    {
        return (new Query())->from('{{%story_readonly_statistics}}')->min('created_at');
    }

}
