<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\StoryBreadcrumbsBuilder;
use backend\components\StorySideBarMenuItemsBuilder;
use backend\MentalMap\MentalMap;
use common\helpers\SmartDate;
use common\models\Story;
use common\rbac\UserRoles;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MentalMapHistoryController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_EDITOR_ACCESS],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<array-key, string>
     */
    private function getStoryMentalMapIds(string $slidesData): array
    {
        $story = (new HTMLReader($slidesData))->load();
        $mentalMapIds = [];
        foreach ($story->getSlides() as $slide) {
            foreach ($slide->getBlocks() as $block) {
                if ($block->isMentalMap()) {
                    /** @var $block HTMLBLock */
                    $content = $block->getContent();
                    $fragment = \phpQuery::newDocumentHTML($content);
                    $mentalMapId = $fragment->find('.mental-map')->attr('data-mental-map-id');
                    $mentalMapIds[] = $mentalMapId;
                }
            }
        }
        return $mentalMapIds;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $story_id): string
    {
        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $mentalMapIds = $this->getStoryMentalMapIds($storyModel->slidesData());

        $mentalMaps = [];
        foreach ($mentalMapIds as $mentalMapId) {
            $mentalMap = MentalMap::findOne($mentalMapId);
            if ($mentalMap !== null) {
                $mentalMaps[] = $mentalMap;
            }
        }

        $subQuery = (new Query())
            ->select([
                'userId' => 'h.user_id',
                'mentalMapId' => 'h.mental_map_id',
                'imageFragmentId' => 'h.image_fragment_id',
                'maxHistoryItemId' => new Expression("SUBSTRING_INDEX(GROUP_CONCAT(h.id ORDER BY h.overall_similarity DESC), ',', 1)"),
                'maxThreshold' => new Expression('MAX(h.threshold)'),
            ])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.story_id' => $storyModel->id,
            ])
            ->groupBy(['h.user_id', 'h.mental_map_id', 'h.image_fragment_id']);

        $historyQuery = (new Query())
            ->select([
                'userId' => 'h.user_id',
                'slideId' => 'h.slide_id',
                'mentalMapId' => 'h.mental_map_id',
                'imageFragmentId' => 'h.image_fragment_id',
                'all' => 'h2.overall_similarity',
                'hiding' => 'h2.text_hiding_percentage',
                'target' => 'h2.text_target_percentage',
                'content' => 'h2.content',
                'createdAt' => 'h2.created_at',
                'maxThreshold' => 't.maxThreshold',
            ])
            ->distinct()
            ->from(['h' => 'mental_map_history'])
            ->innerJoin(['t' => $subQuery], 't.userId = h.user_id AND t.mentalMapId = h.mental_map_id AND t.imageFragmentId = h.image_fragment_id')
            ->innerJoin(['h2' => 'mental_map_history'], 'h2.id = t.maxHistoryItemId')
            ->where([
                'h.story_id' => $storyModel->id,
            ])
            ->groupBy(['h.user_id', 'h.slide_id', 'h.mental_map_id', 'h.image_fragment_id']);

        $query = (new Query())->select([
            't.*',
            'userName' => new Expression(
                "CASE WHEN p.id IS NULL THEN u.email ELSE CONCAT(p.last_name, ' ', p.first_name) END",
            ),
            'slideNumber' => 's.number',
        ])
            ->from(['t' => $historyQuery])
            ->innerJoin(['s' => 'story_slide'], 't.slideId = s.id')
            ->innerJoin(['u' => 'user'], 't.userId = u.id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id');

        $historyByUser = [];
        $users = [];
        foreach ($query->all() as $row) {
            $historyByUser[$row['userId']][] = $row;
            $users[$row['userId']] = ['id' => $row['userId'], 'name' => $row['userName']];
        }

        return $this->render('index', [
            'mentalMaps' => $mentalMaps,
            'historyByUser' => $historyByUser,
            'users' => array_values($users),
            'sidebarMenuItems' => (new StorySideBarMenuItemsBuilder($storyModel))->build(),
            'breadcrumbs' => (new StoryBreadcrumbsBuilder($storyModel, 'Ментальные карты из истории'))->build(),
            'storyId' => $storyModel->id,
        ]);
    }

    public function actionDetail(int $story_id, int $user_id, string $mental_map_id, string $fragment_id): string
    {
        $rows = (new Query())
            ->select('*')
            ->from('mental_map_history')
            ->where([
                'story_id' => $story_id,
                'user_id' => $user_id,
                'mental_map_id' => $mental_map_id,
                'image_fragment_id' => $fragment_id,
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        return $this->renderAjax('detail', [
            'rows' => $rows,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionReport(int $story_id): string
    {
        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $mentalMapIds = $this->getStoryMentalMapIds($storyModel->slidesData());
        $mentalMaps = MentalMap::find()
            ->andWhere(['uuid' => $mentalMapIds])
            ->orderBy(
                new Expression(
                    'FIELD (uuid, ' . implode(
                        ',',
                        array_map(static function (string $id): string {
                            return "'$id'";
                        }, $mentalMapIds),
                    ) . ')',
                ),
            )
            ->all();

        return $this->render('report', [
            'storyName' => $storyModel->title,
            'mentalMaps' => $mentalMaps,
        ]);
    }

    public function actionMapReport(string $id, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            return ['success' => false, 'rows' => []];
        }

        $failedFragmentsQuery= (new Query())
            ->select(new Expression('COUNT(t2.id)'))
            ->from(['t2' => 'mental_map_history'])
            ->where('t2.mental_map_id = t.mental_map_id')
            ->andWhere('t2.image_fragment_id = t.image_fragment_id')
            ->andWhere('t2.overall_similarity < t2.threshold');

        $rows = (new Query())
            ->select([
                'fragmentId' => 't.image_fragment_id',
                'userIds' => new Expression('GROUP_CONCAT(DISTINCT t.user_id ORDER BY t.created_at ASC)'),
                'fragmentsCount' => new Expression('COUNT(t.image_fragment_id)'),
                'fragmentsCorrectCount' => $failedFragmentsQuery,
            ])
            ->from(['t' => 'mental_map_history'])
            ->where([
                't.mental_map_id' => $mentalMap->uuid,
            ])
            ->groupBy(['t.image_fragment_id'])
            ->all();

        $userIds = [];
        array_map(static function(array $row) use (&$userIds): void {
            foreach (explode(',', $row['userIds']) as $id) {
                if (!in_array((int) $id, $userIds, true)) {
                    $userIds[] = (int) $id;
                }
            }
        }, $rows);

        if (count($userIds) > 0) {
            $users = (new Query())
                ->select([
                    'userId' => 'u.id',
                    'userName' => new Expression(
                        "CASE WHEN p.id IS NULL THEN u.email ELSE CONCAT(p.last_name, ' ', p.first_name) END",
                    ),
                ])
                ->from(['u' => 'user'])
                ->leftJoin(['p' => 'profile'], 'u.id = p.user_id')
                ->where(['in', 'u.id', $userIds])
                ->all();
            $users = array_combine(array_column($users, 'userId'), array_column($users, 'userName'));
            $rows = array_map(static function(array $row) use ($users): array {
                $row['userNames'] = implode(', ', array_map(static function(int $userId) use ($users): string {
                    return $users[$userId];
                }, explode(',', $row['userIds'])));
                return $row;
            }, $rows);
        }

        return [
            'success' => true,
            'rows' => $rows,
        ];
    }

    public function actionMapReportDetail(string $map_id, string $fragment_id): string
    {
        $rows = (new Query())
            ->select([
                'userName' => new Expression(
                    "CASE WHEN p.id IS NULL THEN u.email ELSE CONCAT(p.last_name, ' ', p.first_name) END",
                ),
                'user_id' => 't.user_id',
                'content' => 't.content',
                'overall_similarity' => 't.overall_similarity',
                'text_hiding_percentage' => 't.text_hiding_percentage',
                'text_target_percentage' => 't.text_target_percentage',
                'threshold' => 't.threshold',
                'created_at' => 't.created_at',
            ])
            ->from(['t' => 'mental_map_history'])
            ->innerJoin(['u' => 'user'], 'u.id = t.user_id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id')
            ->where([
                't.mental_map_id' => $map_id,
                't.image_fragment_id' => $fragment_id,
            ])
            ->orderBy(['t.created_at' => SORT_DESC])
            ->all();
        return $this->renderAjax('report_detail', [
            'rows' => $rows,
        ]);
    }

    public function actionLog(int $user_id, string $date, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        $dateFrom = new \DateTimeImmutable(Yii::$app->formatter->asDatetime($date));
        $betweenBegin = new Expression("UNIX_TIMESTAMP('" . $dateFrom->format('Y-m-d H:i:00') . "')");
        $betweenEnd = new Expression("UNIX_TIMESTAMP('" . $dateFrom->format('Y-m-d H:i:59') . "')");

        $rows = (new Query())
            ->select([
                'created_at',
                'input' => new Expression('IF(t.input -> "$.input.question" IS NULL,
                      JSON_UNQUOTE(JSON_EXTRACT(t.input, CONCAT("$.input.messages[", JSON_LENGTH(input ->> "$.input.messages") -1 ,"].content"))),
                      JSON_UNQUOTE(t.input -> "$.input.question")
                    )'),
                'output' => new Expression('CASE
                        WHEN t.output->"$.final_output" IS NULL
                        THEN JSON_UNQUOTE(t.output->"$.message")
                        ELSE JSON_UNQUOTE(t.output->"$.final_output")
                    END')
            ])
            ->from(['t' => 'llm_feedback'])
            ->where([
                'user_id' => $user_id,
            ])
            ->andWhere(['between', 't.created_at + (3 * 60 * 60)', $betweenBegin, $betweenEnd])
            ->all();

        $rows = array_map(static function(array $row): array {
            $row['created_at'] = SmartDate::dateSmart($row['created_at'], true);
            return $row;
        }, $rows);

        return ['success' => true, 'rows' => $rows];
    }
}
