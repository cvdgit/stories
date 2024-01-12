<?php

declare(strict_types=1);

namespace backend\Story\Tests\UpdatePassTestsRepeat;

use backend\models\question\QuestionType;
use backend\Story\Tests\StorySourceTestsFetcher;
use common\models\Story;
use yii\base\Action;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class UpdatePassTestsRepeatFormAction extends Action
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
                "testName" => "st.title",
                "questionId" => "stq.id",
                "questionName" => "stq.name",
                "questionPrevItems" => "stq.max_prev_items",
            ])
            ->from(["st" => "story_test"])
            ->innerJoin(["stq" => "story_test_question"], "st.id = stq.story_test_id")
            ->where(["in", "st.id", $testIds])
            ->andWhere(["stq.type" => QuestionType::PASS_TEST]);

        $form = new UpdatePassTestsRepeatForm();

        return $this->controller->renderAjax("_update_pass_tests_repeat_form", [
            "storyId" => $storyModel->id,
            "rows" => $query->all(),
            "formModel" => $form,
        ]);
    }
}
