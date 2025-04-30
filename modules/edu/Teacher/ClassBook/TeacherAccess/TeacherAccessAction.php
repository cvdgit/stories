<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

use common\models\User;
use Exception;
use modules\edu\models\EduClassBook;
use Yii;
use yii\base\Action;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

class TeacherAccessAction extends Action
{
    public function run(Request $request, Response $response): array
    {
        $response->format = Response::FORMAT_JSON;
        $accessForm = new TeacherAccessForm();
        if ($accessForm->load($request->post())) {
            if (!$accessForm->validate()) {
                return ['success' => false, 'message' => 'not valid'];
            }
            try {

                $user = User::findOne($accessForm->teacher_id);
                if (!$user) {
                    throw new NotFoundHttpException('User not found');
                }

                $classBook = EduClassBook::findOne($accessForm->class_book_id);
                if (!$classBook) {
                    throw new NotFoundHttpException('ClassBook not found');
                }

                $command = Yii::$app->db->createCommand();
                $command->insert('edu_class_book_teacher_access', [
                    'class_book_id' => $classBook->id,
                    'teacher_id' => $user->id,
                    'created_at' => time(),
                ]);
                $command->execute();

                return [
                    'success' => true,
                    'message' => 'success',
                    'data' => [
                        'name' => $user->getProfileName(),
                        'class_book_id' => $classBook->id,
                        'teacher_id' => $user->id,
                    ],
                ];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false, 'message' => $exception->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'no data'];
    }
}
