<?php

declare(strict_types=1);

namespace frontend\controllers;

use Exception;
use frontend\Retelling\RetellingHistoryForm;
use Yii;
use yii\helpers\Json;
use yii\rest\Controller;
use yii\web\Request;
use yii\web\User as WebUser;

class RetellingController extends Controller
{
    public function actionSave(WebUser $user, Request $request): array
    {
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
                return ['success' => true, 'completed' => (int) $form->overall_similarity > 90];
            } catch (Exception $ex) {
                Yii::$app->errorHandler->logException($ex);
                return ['success' => false, 'message' => 'При сохранении истории повторения произошла ошибка'];
            }
        }

        return ['success' => false];
    }
}
