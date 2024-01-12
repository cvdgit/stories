<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdateTestsRepeat;

use backend\Story\Tests\StorySourceTestsFetcher;
use common\models\Story;
use yii\base\Action;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class UpdateTestsRepeatFormAction extends Action
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
            ->select([
                "testId" => "id",
                "testName" => "title",
                "testRepeat" => "repeat",
            ])
            ->from("story_test")
            ->where(["in", "id", $testIds]);

        $form = new UpdateTestsRepeatForm();

        return $this->controller->renderAjax("_update_tests_repeat_form", [
            "storyId" => $storyModel->id,
            "rows" => $query->all(),
            "formModel" => $form,
        ]);
    }
}
