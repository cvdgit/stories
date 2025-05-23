<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\StoryBreadcrumbsBuilder;
use backend\components\StorySideBarMenuItemsBuilder;
use backend\MentalMap\MentalMap;
use common\models\Story;
use common\rbac\UserRoles;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $story_id): string
    {
        $storyModel = Story::findOne($story_id);
        if ($storyModel === null) {
            throw new NotFoundHttpException('История не найдена');
        }

        $story = (new HTMLReader($storyModel->slidesData()))->load();
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
            "sidebarMenuItems" => (new StorySideBarMenuItemsBuilder($storyModel))->build(),
            "breadcrumbs" => (new StoryBreadcrumbsBuilder($storyModel, "Ментальные карты из истории"))->build(),
        ]);
    }
}
