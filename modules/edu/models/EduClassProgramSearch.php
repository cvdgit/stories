<?php

declare(strict_types=1);

namespace modules\edu\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\Query;

/**
 * EduClassProgramSearch represents the model behind the search form of `modules\edu\models\EduClassProgram`.
 */
class EduClassProgramSearch extends Model
{
    public $class_id;
    public $program_id;
    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['class_id', 'program_id'], 'integer'],
        ];
    }

    public function search(array $params): DataProviderInterface
    {
        $query = EduClassProgram::find()
            ->innerJoinWith(['class', 'program']);

        $query->addSelect([
            'edu_class_program.*',
            'topicsTotal' => (new Query())->select('COUNT(edu_topic.id)')->from('edu_topic')->where('edu_topic.class_program_id = edu_class_program.id'),
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => ['cpId' => SORT_DESC],
                'attributes' => [
                    'cpId' => [
                        'asc' => ['edu_class_program.id' => SORT_ASC],
                        'desc' => ['edu_class_program.id' => SORT_DESC],
                    ],
                    'class.name' => [
                        'asc' => ['edu_class.name' => SORT_ASC],
                        'desc' => ['edu_class.name' => SORT_DESC],
                    ],
                    'program.name' => [
                        'asc' => ['edu_program.name' => SORT_ASC],
                        'desc' => ['edu_program.name' => SORT_DESC],
                    ],
                    'topicsTotal',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'class_id' => $this->class_id,
            'program_id' => $this->program_id,
        ]);

        return $dataProvider;
    }
}
