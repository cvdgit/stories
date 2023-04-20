<?php

declare(strict_types=1);

namespace backend\modules\changelog\TagList;

use yii\base\Model;
use yii\db\Query;

class TagListSearch extends Model
{
    public $query;

    public function rules(): array
    {
        return [
            ['query', 'required'],
            ['query', 'string', 'max' => 30],
        ];
    }

    public function search(array $params): array
    {
        $this->load($params, '');
        if (!$this->validate()) {
            return [];
        }
        return (new Query())
            ->select('name')
            ->from('tag')
            ->where(['like', 'name', '%' . $this->query . '%', false])
            ->orderBy(['name' => SORT_ASC])
            ->limit(10)
            ->all();
    }
}
