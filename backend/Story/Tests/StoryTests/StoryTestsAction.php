<?php

declare(strict_types=1);

namespace backend\Story\Tests\StoryTests;

use backend\components\StoryBreadcrumbsBuilder;
use backend\components\StorySideBarMenuItemsBuilder;
use backend\Story\Tests\StorySourceTestsFetcher;
use common\models\Story;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class StoryTestsAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $id): string
    {
        $storyModel = Story::findOne($id);
        if ($storyModel === null) {
            throw new NotFoundHttpException("История не найдена");
        }

        $testIds = (new StorySourceTestsFetcher())->fetch($storyModel->slidesData());

        $query = (new Query())
            ->select("*")
            ->from("story_test")
            ->where(["in", "id", $testIds]);

        $dataProvider = new ActiveDataProvider([
            "query" => $query,
        ]);

        return $this->controller->render('index', [
            "dataProvider" => $dataProvider,
            "sidebarMenuItems" => (new StorySideBarMenuItemsBuilder($storyModel))->build(),
            "breadcrumbs" => (new StoryBreadcrumbsBuilder($storyModel, "Тесты из истории"))->build(),
            "storyId" => $storyModel->id,
            "title" => "Тесты из истории",
        ]);
    }
}
