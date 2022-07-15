<?php

namespace backend\models\audio_file;

use common\models\AudioFile;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AudioFileSearch extends Model
{

    public $name;
    public $path;
    public $created_at;

    public function rules(): array
    {
        return [
            [['name', 'path'], 'string', 'max' => 255],
            ['created_at', 'datetime', 'format' => 'php:d.m.Y'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = AudioFile::find();
        $query->distinct();
        $query->joinWith('storyTestQuestions.storyTest');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['audio_file.created_at' => SORT_DESC],
            ],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'audio_file.name', $this->name]);
        $query->andFilterWhere(['or',
            ['like', 'story_test_question.name', $this->path],
            ['like', 'story_test.title', $this->path]
        ]);
        $query->andFilterWhere(["DATE_FORMAT(FROM_UNIXTIME(audio_file.created_at), '%d.%m.%Y')" => $this->created_at]);
        return $dataProvider;
    }
}
