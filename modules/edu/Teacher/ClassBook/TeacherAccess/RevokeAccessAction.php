<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

use common\models\User;
use Exception;
use modules\edu\models\EduClassBook;
use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class RevokeAccessAction extends Action
{
    public function run(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;
        $revokeForm = new RevokeAccessForm();
        $payload = Json::decode($request->rawBody);
        if ($revokeForm->load($payload, '')) {
            if (!$revokeForm->validate()) {
                return ['success' => false, 'message' => 'Not valid'];
            }

            $classBook = EduClassBook::findClassBook((int) $revokeForm->classBookId, $user->getId());
            if ($classBook === null) {
                return ['success' => false, 'message' => 'ClassBook not found'];
            }

            $teacher = User::findUser((int) $revokeForm->teacherId);
            if ($teacher === null) {
                return ['success' => false, 'message' => 'Teacher not found'];
            }

            try {
                $command = Yii::$app->db->createCommand();
                $command->delete('edu_class_book_teacher_access', [
                    'class_book_id' => $classBook->id,
                    'teacher_id' => $teacher->id,
                ]);
                $command->execute();
                return ['success' => true];
            } catch (Exception $exception) {
                Yii::$app->errorHandler->logException($exception);
                return ['success' => false];
            }
        }
        return ['success' => false, 'message' => 'No data'];
    }
}
