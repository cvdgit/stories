<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\components\story\HTMLBLock;
use backend\components\story\reader\HTMLReader;
use backend\components\story\TestBlock;
use backend\MentalMap\MentalMap;
use common\models\Story;
use common\rbac\UserRoles;
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

        $query = (new Query())->select([
            'h.*',
            'userName' => new Expression(
                "CASE WHEN p.id IS NULL THEN u.email ELSE CONCAT(p.last_name, ' ', p.first_name) END",
            ),
            'slideNumber' => 's.number',
        ])
            ->from(['h' => 'mental_map_history'])
            ->innerJoin(['s' => 'story_slide'], 'h.slide_id = s.id')
            ->innerJoin(['u' => 'user'], 'h.user_id = u.id')
            ->leftJoin(['p' => 'profile'], 'u.id = p.user_id')
            ->where([
                'h.story_id' => $storyModel->id,
            ])
            ->andWhere([
                'in',
                'h.mental_map_id',
                $mentalMapIds,
            ])
            ->orderBy([
                's.number' => SORT_ASC,
                'h.created_at' => SORT_DESC,
            ]);

        $historyByUser = [];
        $users = [];
        foreach ($query->all() as $row) {
            $historyByUser[$row['user_id']][] = $row;
            $users[$row['user_id']] = ['id' => $row['user_id'], 'name' => $row['userName']];
        }

        return $this->render('index', [
            'mentalMaps' => $mentalMaps,
            'historyByUser' => $historyByUser,
            'users' => array_values($users),
        ]);
    }
}
