<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Story;
use Exception;
use frontend\MentalMap\MentalMap;
use frontend\MentalMap\MentalMapHistoryForm;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class MentalMapController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionInit(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $rawBody = Json::decode($request->rawBody);
        $id = $rawBody['id'];
        if (!Uuid::isValid($id)) {
            return ['success' => false, 'message' => 'Id not valid'];
        }
        $mentalMap = MentalMap::findOne($id);
        if ($mentalMap === null) {
            return ['success' => false, 'message' => 'Mental Map not found'];
        }

        $storyId = (int) $rawBody['story_id'];
        if (Story::findOne($storyId) === null) {
            return ['success' => false, 'message' => 'Story not found'];
        }

        $images = [];
        if (isset($mentalMap->payload['map'])) {
            $map = $mentalMap->payload['map'];
            if (isset($map['images']) && is_array($map['images'])) {
                $images = $map['images'];
            }
        }
        $history = array_map(static function (array $image): array {
            return [
                'id' => $image['id'],
                'all' => 0,
                'hiding' => 0,
                'target' => 0,
            ];
        }, $images);

        $rows = (new Query())
            ->select([
                'id' => 'h.image_fragment_id',
                'all' => 'MAX(h.overall_similarity)',
                'hiding' => 'MAX(h.text_hiding_percentage)',
                'target' => 'MAX(h.text_target_percentage)',
            ])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.story_id' => $storyId,
                'h.mental_map_id' => $mentalMap->uuid,
                'h.user_id' => $user->getId(),
            ])
            ->groupBy('h.image_fragment_id')
            ->indexBy('id')
            ->all();

        $history = array_map(static function (array $item) use ($rows): array {
            if (isset($rows[$item['id']])) {
                $item['all'] = (int) $rows[$item['id']]['all'];
                $item['hiding'] = (int) $rows[$item['id']]['hiding'];
                $item['target'] = (int) $rows[$item['id']]['target'];
            }
            return $item;
        }, $history);

        return ['success' => true, 'mentalMap' => $mentalMap->payload, 'history' => $history];
    }

    public function actionSave(WebUser $user, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        if ($user->isGuest) {
            return ['success' => false, 'message' => 'Forbidden'];
        }

        $rawBody = Json::decode($request->rawBody);

        $form = new MentalMapHistoryForm();
        if ($form->load($rawBody, '')) {
            if (!$form->validate()) {
                return ['success' => false, 'message' => $form->getErrorSummary(true)];
            }

            try {
                $command = Yii::$app->db->createCommand();
                $command->insert('mental_map_history', [
                    'id' => Uuid::uuid4()->toString(),
                    'story_id' => $form->story_id,
                    'slide_id' => $form->slide_id,
                    'mental_map_id' => $form->mental_map_id,
                    'image_fragment_id' => $form->image_fragment_id,
                    'user_id' => $user->getId(),
                    'content' => $form->content,
                    'overall_similarity' => $form->overall_similarity,
                    'text_hiding_percentage' => $form->text_hiding_percentage,
                    'text_target_percentage' => $form->text_target_percentage,
                    'created_at' => time(),
                ]);
                $command->execute();

                $query = (new Query())
                    ->select([
                        'id' => 'h.image_fragment_id',
                        'all' => 'MAX(h.overall_similarity)',
                        'hiding' => 'MAX(h.text_hiding_percentage)',
                        'target' => 'MAX(h.text_target_percentage)',
                    ])
                    ->from(['h' => 'mental_map_history'])
                    ->where([
                        'h.story_id' => $form->story_id,
                        'h.mental_map_id' => $form->mental_map_id,
                        'h.user_id' => $user->getId(),
                        'h.image_fragment_id' => $form->image_fragment_id,
                    ])
                    ->groupBy('h.image_fragment_id');

                return ['success' => true, 'history' => $query->one()];
            } catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'message' => 'При сохранении истории карты знаний произошла ошибка'];
            }
        }

        return ['success' => false];
    }
}
