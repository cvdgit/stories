<?php


namespace backend\models;


use common\models\SlideVideo;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SlideVideoSearch extends Model
{

    public $video_id;

    public function rules()
    {
        return [
            ['video_id', 'string'],
        ];
    }

    public function search(array $params)
    {
        $query = SlideVideo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'video_id', $this->video_id]);

        return $dataProvider;
    }

}