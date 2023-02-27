<?php

declare(strict_types=1);

namespace backend\Testing;

use backend\Testing\columns\ColumnsMapper;
use backend\Testing\columns\DefaultColumnsList;
use backend\Testing\columns\NeoColumnsList;
use backend\Testing\columns\TestsColumnsList;
use backend\Testing\columns\WordColumnList;
use common\models\test\SourceType;
use common\services\TestHistoryService;
use yii\base\Action;
use yii\web\Request;
use yii\web\User as WebUser;

class IndexAction extends Action
{
    private $historyService;

    public function __construct($id, $controller, TestHistoryService $historyService, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->historyService = $historyService;
    }

    public function run(Request $request, WebUser $user, int $source = SourceType::TEST): string
    {
        $searchModel = new TestSearch([
            'source' => $source,
            'created_by' => $user->getId(),
        ]);

        //$dataProvider = $searchModel->search($user->id, $request->isPjax ? $request->post() : $request->get());
        $dataProvider = $searchModel->search($user->id, $request->get());

        $list = (new ColumnsMapper([
            SourceType::TEST => new DefaultColumnsList($searchModel),
            SourceType::NEO => new NeoColumnsList($searchModel),
            SourceType::LIST => new WordColumnList($searchModel),
            SourceType::TESTS => new TestsColumnsList($searchModel),
        ]))->createColumns($source);

        return $this->controller->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'source' => $source,
            'sourceRecordsTotal' => $this->historyService->getRecordsCountBySource($source),
            'columns' => $list->getList(),
        ]);
    }
}
