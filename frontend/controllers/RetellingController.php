<?php

declare(strict_types=1);

namespace frontend\controllers;

use backend\components\story\AbstractBlock;
use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\TextBlock;
use Exception;
use frontend\Retelling\Retelling;
use frontend\Retelling\RetellingHistoryForm;
use Ramsey\Uuid\Uuid;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class RetellingController extends Controller
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
        $retelling = Retelling::findOne($id);
        if ($retelling === null) {
            return ['success' => false, 'message' => 'Retelling not found'];
        }

        $slideContent = (new Query())
            ->select('t.data')
            ->from(['t' => 'story_slide'])
            ->where(['t.id' => $retelling->slide_id])
            ->scalar();

        $slide = (new HtmlSlideReader($slideContent))->load();
        $texts = [];
        foreach ($slide->getBlocks() as $slideBlock) {
            if ($slideBlock->typeIs(AbstractBlock::TYPE_TEXT)) {
                /** @var $slideBlock TextBlock */
                $text = $slideBlock->getText();
                if ($text !== '') {
                    $texts[] = strip_tags(trim($text));
                }
            }
        }

        $slideId = $rawBody['slide_id'];
        $storyId = $rawBody['story_id'];

        $completed = (new Query())
            ->select([
                'overallSimilarity' => new Expression('MAX(rh.overall_similarity)'),
            ])
            ->from(['rh' => 'retelling_history'])
            ->where([
                'story_id' => $storyId,
                'slide_id' => $slideId,
                'user_id' => $user->getId(),
            ])
            ->andWhere('rh.overall_similarity >= 90')
            ->scalar();

        return [
            'success' => true,
            'withQuestions' => $retelling->with_questions === 1,
            'text' => implode("\n", $texts),
            'completed' => $completed !== null,
            'all' => (int) $completed,
            'questions' => $retelling->questions,
        ];
    }

    public function actionSave(WebUser $user, Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;

        if ($user->isGuest) {
            return ['success' => false, 'message' => 'Forbidden'];
        }

        $rawBody = Json::decode($request->rawBody);

        $form = new RetellingHistoryForm();
        if ($form->load($rawBody, '')) {
            if (!$form->validate()) {
                return ['success' => false, 'message' => $form->getErrorSummary(true)];
            }

            try {
                $command = Yii::$app->db->createCommand();
                $command->insert('retelling_history', [
                    'story_id' => $form->story_id,
                    'user_id' => $user->getId(),
                    'key' => md5($form->content),
                    'slide_id' => $form->slide_id,
                    'overall_similarity' => $form->overall_similarity,
                    'created_at' => time(),
                ]);
                $command->execute();
                return [
                    'success' => true,
                    'completed' => (int) $form->overall_similarity >= 90,
                    'all' => (int) $form->overall_similarity,
                ];
            } catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'message' => 'При сохранении истории повторения произошла ошибка'];
            }
        }

        return ['success' => false];
    }
}
