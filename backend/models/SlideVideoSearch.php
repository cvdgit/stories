<?php

namespace backend\models;

use backend\models\video\VideoSource;
use common\models\SlideVideo;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SlideVideoSearch extends Model
{

    public $title;
    public $video_id;
    public $created_at;
    public $status;
    public $source;

    public function rules()
    {
        return [
            [['video_id', 'title'], 'string'],
            ['created_at', 'safe'],
            [['status', 'source'], 'integer'],
            ['source', 'in', 'range' => VideoSource::getTypes()],
        ];
    }

    public function search(array $params)
    {
        $query = SlideVideo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'video_id', $this->video_id]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere([
            'source' => $this->source,
            "DATE_FORMAT(FROM_UNIXTIME(created_at), '%d.%m.%Y')" => $this->created_at,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

    public function sourceIsFile(): bool
    {
        return (int) $this->source === VideoSource::FILE;
    }

    public function sourceIsYouTube(): bool
    {
        return (int) $this->source === VideoSource::YOUTUBE;
    }

}