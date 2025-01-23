<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\Story;
use common\models\User;
use common\rbac\UserRoles;
use Exception;
use frontend\MentalMap\MentalMap;
use frontend\MentalMap\MentalMapHistoryForm;
use frontend\MentalMap\repetition\MentalMapFinishCommand;
use frontend\MentalMap\repetition\MentalMapFinishForm;
use frontend\MentalMap\repetition\MentalMapFinishHandler;
use frontend\MentalMap\repetition\StartMentalMapRepetitionCommand;
use frontend\MentalMap\repetition\StartMentalMapRepetitionHandler;
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
    /**
     * @var StartMentalMapRepetitionHandler
     */
    private $startMentalMapRepetitionHandler;
    /**
     * @var MentalMapFinishHandler
     */
    private $mentalMapFinishHandler;

    public function __construct(
        $id,
        $module,
        StartMentalMapRepetitionHandler $startMentalMapRepetitionHandler,
        MentalMapFinishHandler $mentalMapFinishHandler,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->startMentalMapRepetitionHandler = $startMentalMapRepetitionHandler;
        $this->mentalMapFinishHandler = $mentalMapFinishHandler;
    }

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

        $repetitionMode = $rawBody['repetition_mode'] ?? false;


        //$treeHistory = $this->createMentalMapTreeHistory($list, $mentalMap->uuid, $user->getId());
        //die(print_r($treeHistory));

        if ($mentalMap->isMentalMapAsTree()) {
            $list = $this->flatten($mentalMap->getTreeData());
            if (!$repetitionMode) {
                $list = $this->createMentalMapTreeHistory($list, $mentalMap->uuid, $user->getId());
            }
            $history = array_map(static function (array $item): array {
                return [
                    'id' => $item['id'],
                    'done' => $item['done'] ?? false,
                    //'all' => $item['all'] ?? 0,
                    //'hiding' => $item['hiding'] ?? 0,
                    //'target' => $item['target'] ?? 0,
                ];
            }, $list);
        } else {
            $items = $mentalMap->getImages();
            if (!$repetitionMode) {
                $items = $this->createMentalMapHistory($items, $mentalMap->uuid, $user->getId());
            }
            $history = array_map(static function (array $item): array {
                return [
                    'id' => $item['id'],
                    'all' => $item['all'] ?? 0,
                    'hiding' => $item['hiding'] ?? 0,
                    'target' => $item['target'] ?? 0,
                ];
            }, $items);
        }

        $prompt = null;
        if ($user->can(UserRoles::ROLE_ADMIN)) {
            $prompt = (new Query())
                ->select('t.prompt')
                ->from(['t' => 'llm_prompt'])
                ->where(['t.key' => 'text-rewrite'])
                ->scalar();
        }

        return [
            'success' => true,
            'mentalMap' => $mentalMap->payload,
            'history' => $history,
            'rewritePrompt' => $prompt,
        ];
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

            $mentalMap = MentalMap::findOne($form->mental_map_id);
            if ($mentalMap === null) {
                return ['success' => false, 'message' => 'Mental map not found'];
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
                        'all' => 'h.overall_similarity',
                        'hiding' => 'h.text_hiding_percentage',
                        'target' => 'h.text_target_percentage',
                    ])
                    ->from(['h' => 'mental_map_history'])
                    ->where([
                        //'h.story_id' => $form->story_id,
                        'h.mental_map_id' => $form->mental_map_id,
                        'h.user_id' => $user->getId(),
                        'h.image_fragment_id' => $form->image_fragment_id,
                    ])
                    ->orderBy(['h.created_at' => SORT_DESC])
                    ->limit(1);
                $fragmentHistory = $query->one();

                if (!$form->repetition_mode) {
                    $history = $this->createMentalMapHistory(
                        $mentalMap->getImages(),
                        $mentalMap->uuid,
                        $user->getId(),
                    );
                    if (MentalMap::isDone($history)) {
                        $currentUser = User::findOne($user->getId());
                        if ($currentUser === null) {
                        }
                        $this->startMentalMapRepetitionHandler->handle(
                            new StartMentalMapRepetitionCommand($mentalMap->uuid, $currentUser->getStudentID()),
                        );
                    }
                }

                return ['success' => true, 'history' => $fragmentHistory];
            } catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'message' => 'При сохранении истории карты знаний произошла ошибка'];
            }
        }

        return ['success' => false];
    }

    private function createMentalMapHistory(array $images, string $mentalMapId, int $userId): array
    {
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
                'h.mental_map_id' => $mentalMapId,
                'h.user_id' => $userId,
            ])
            ->groupBy('h.image_fragment_id')
            ->indexBy('id')
            ->all();

        return array_map(static function (array $item) use ($rows): array {
            if (isset($rows[$item['id']])) {
                $item['all'] = (int) $rows[$item['id']]['all'];
                $item['hiding'] = (int) $rows[$item['id']]['hiding'];
                $item['target'] = (int) $rows[$item['id']]['target'];
            }
            return $item;
        }, $history);
    }

    private function createMentalMapTreeHistory(array $nodeList, string $mentalMapId, int $userId): array
    {
        $history = array_map(static function (array $node): array {
            return [
                'id' => $node['id'],
                'done' => false,
            ];
        }, $nodeList);

        $rows = (new Query())
            ->select([
                'id' => 'h.image_fragment_id',
                'all' => 'MAX(h.overall_similarity)',
                'hiding' => 'MAX(h.text_hiding_percentage)',
                'target' => 'MAX(h.text_target_percentage)',
            ])
            ->from(['h' => 'mental_map_history'])
            ->where([
                'h.mental_map_id' => $mentalMapId,
                'h.user_id' => $userId,
            ])
            ->groupBy('h.image_fragment_id')
            ->indexBy('id')
            ->all();

        return array_map(static function (array $item) use ($rows): array {
            if (isset($rows[$item['id']])) {
                $item['done'] = (int) $rows[$item['id']]['all'] > 50;
                $item['all'] = (int) $rows[$item['id']]['all'];
                $item['hiding'] = (int) $rows[$item['id']]['hiding'];
                $item['target'] = (int) $rows[$item['id']]['target'];
            }
            return $item;
        }, $history);
    }

    public function actionFinish(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $finishForm = new MentalMapFinishForm();
        $rawBody = Json::decode($request->rawBody);
        if ($finishForm->load($rawBody, '')) {
            if (!$finishForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }
            try {
                $currentUser = User::findOne($user->getId());
                if ($currentUser === null) {
                }
                $this->mentalMapFinishHandler->handle(new MentalMapFinishCommand($finishForm->mental_map_id, $currentUser->getStudentID()));
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false];
            }
        }
        return ['success' => false];
    }

    public function actionUpdateRewritePrompt(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $payload = Json::decode($request->rawBody);
        $prompt = $payload['prompt'];

        $command = Yii::$app->db->createCommand();
        $command->update('llm_prompt', ['prompt' => $prompt], ['key' => 'text-rewrite']);
        $command->execute();

        return ['success' => true];
    }

    private function flatten(array $element): array
    {
        $flatArray = [];
        foreach ($element as $key => $node) {
            if (array_key_exists('children', $node)) {
                $flatArray = array_merge($flatArray, $this->flatten($node['children'] ?? []));
                unset($node['children']);
            }
            $flatArray[] = $node;
        }
        return $flatArray;
    }
}
