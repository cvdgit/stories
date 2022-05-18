<?php

namespace backend\models\question\sequence;

use backend\models\question\QuestionModel;

class SequenceQuestion extends QuestionModel
{
    public $sort_view;

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['sort_view', 'in', 'range' => SortView::values()],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sort_view' => 'Список',
        ]);
    }

    public function getSortViewValues(): array
    {
        return SortView::texts();
    }
}
