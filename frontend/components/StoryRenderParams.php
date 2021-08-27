<?php

namespace frontend\components;

use common\models\Category;
use common\models\SiteSection;
use frontend\models\StorySearchInterface;

class StoryRenderParams
{

    private $params = [
        'section' => null,
        'category' => null,
        'searchModel' => null,
        'dataProvider' => null,
        'emptyText' => 'Список историй пуст',
        'action' => [],
    ];

    public function setSectionModel(SiteSection $sectionModel): self
    {
        $this->params['section'] = $sectionModel;
        return $this;
    }

    public function setCategoryModel(Category $categoryModel): self
    {
        $this->params['category'] = $categoryModel;
        return $this;
    }

    public function setSearchModel(StorySearchInterface $searchModel, array $queryParams): self
    {
        $this->params['searchModel'] = $searchModel;
        $this->params['dataProvider'] = $searchModel->search($queryParams);
        return $this;
    }

    public function setEmptyText(string $emptyText): self
    {
        $this->params['emptyText'] = $emptyText;
        return $this;
    }

    public function setSearchAction(array $searchAction): self
    {
        $this->params['action'] = $searchAction;
        return $this;
    }

    public function asArray(): array
    {
        return $this->params;
    }
}
