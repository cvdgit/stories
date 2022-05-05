<?php

namespace backend\models\audio_file;

use common\models\AudioFile;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AudioFileSearch extends Model
{

    public $name;
    public $path;

    public function rules(): array
    {
        return [
            [['name', 'path'], 'string', 'max' => 255],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = AudioFile::find();
        $query->joinWith('storyTestQuestions.storyTest');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['or',
            ['like', 'story_test_question.name', $this->path],
            ['like', 'story_test.title', $this->path]
        ]);
        return $dataProvider;
    }
}
