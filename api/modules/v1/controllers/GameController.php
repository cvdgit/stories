<?php

declare(strict_types=1);

namespace api\modules\v1\controllers;

use api\modules\v1\models\Story;
use api\modules\v1\models\StoryTest;
use common\models\StoryStoryTest;
use common\models\StudentQuestionProgress;
use common\models\User;
use common\models\UserStoryHistory;
use PDO;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class GameController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ]
        ];
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     * @throws BadRequestHttpException
     */
    public function actionView(int $id, Response $response): array
    {
        $userModel = User::findOne($id);
        if ($userModel === null) {
            throw new NotFoundHttpException("User not found");
        }

        $command = Yii::$app->db->createCommand("SELECT data FROM game_data WHERE user_id=:id");
        $command->bindValue(":id", $id, PDO::PARAM_INT);
        $result = $command->queryOne();
        if ($result === false) {
            throw new NotFoundHttpException("Game data not found");
        }
        $data = Json::decode($result['data']);

        $stories = (new Query())
            ->select([
                "storyTitle" => "s.title",
                "storyId" => "s.id",
                "storyProgress" => "story_history.percent",
            ])
            ->from(["story_history" => UserStoryHistory::tableName()])
            ->innerJoin(["s" => Story::tableName()], "story_history.story_id = s.id")
            ->where([
                "story_history.user_id" => $userModel->id,
            ])
            //->andWhere("s.id = 1980")
            ->all();

        $progress = [];

        $students = $userModel->students;
        if (count($students) === 0) {
            throw new BadRequestHttpException("Students not found");
        }
        $student = $students[0];

        foreach ($stories as $story) {

            $storyProgress = [
                "id" => (int) $story["storyId"],
                "title" => $story["storyTitle"],
                "completed" => (int) $story["storyProgress"] === 100,
            ];

            $studentTests = (new Query())
                ->select([
                    "testId" => "student_progress.test_id",
                    "testTitle" => "t.title",
                    "testProgress" => "student_progress.progress",
                ])
                ->from(["story_test" => StoryStoryTest::tableName()])
                ->innerJoin(["student_progress" => StudentQuestionProgress::tableName()],
                    "story_test.test_id = student_progress.test_id")
                ->innerJoin(["t" => StoryTest::tableName()], "student_progress.test_id = t.id")
                ->where([
                    "story_test.story_id" => (int) $story["storyId"],
                    "student_progress.student_id" => $student->id,
                ])
                ->all();

            $storyProgress["tests"] = array_map(static function (array $test): array {
                return [
                    "id" => (int) $test["testId"],
                    "title" => $test["testTitle"],
                    "completed" => (int) $test["testProgress"] === 100,
                ];
            }, $studentTests);

            $progress[] = $storyProgress;
        }

        $data["progress"] = $progress;

        return $data;
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function actionCreate(Request $request): array
    {
        $payload = Json::decode($request->rawBody);
        $id = $payload["id"] ?? null;
        if ($id === null) {
            throw new BadRequestHttpException("ID field must be set ");
        }

        $command = Yii::$app->db->createCommand();
        $command->insert("game_data", [
            "user_id" => $id,
            "data" => $payload,
        ]);
        $sql = $command->getRawSql();
        $sql .= " ON DUPLICATE KEY UPDATE `data`=VALUES(`data`)";
        $command->setSql($sql);
        $command->execute();

        return [];
    }
}
