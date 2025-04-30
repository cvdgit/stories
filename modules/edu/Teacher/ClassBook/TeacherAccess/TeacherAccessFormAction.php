<?php

declare(strict_types=1);

namespace modules\edu\Teacher\ClassBook\TeacherAccess;

use common\models\User;
use common\rbac\UserRoles;
use modules\edu\models\EduClassBook;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\User as WebUser;

class TeacherAccessFormAction extends Action
{
    /**
     * @throws NotFoundHttpException
     */
    public function run(int $class_book_id, WebUser $user): string
    {
        $classBook = EduClassBook::findClassBook($class_book_id, $user->getId());
        if ($classBook === null) {
            throw new NotFoundHttpException('ClassBook not found');
        }

        $teacherIds = (new Query())
            ->select([
                'teacherId' => 't.teacher_id',
            ])
            ->from(['t' => 'edu_class_book_teacher_access'])
            ->where([
                't.class_book_id' => $classBook->id,
            ])
            ->orderBy(['t.created_at' => SORT_ASC])
            ->all();
        $teacherIds = array_column($teacherIds, 'teacherId');

        $access = [];
        $teachersQuery = User::find();
        $teachersQuery->innerJoin(['t' => 'auth_assignment'], 'user.id = t.user_id');
        $teachersQuery->andWhere([
            't.item_name' => UserRoles::ROLE_TEACHER,
            'user.status' => 10,
        ]);
        if (count($teacherIds) > 0) {
            $access = array_map(static function(User $u): UserItem {
                return new UserItem($u->id, $u->getProfileName(), $u->email, $u->getProfilePhoto());
            }, User::find()->andWhere(['in', 'id', $teacherIds])->all());
            $teachersQuery->andWhere(['not in', 'user.id', $teacherIds]);
        }

        $teachers = array_map(static function(User $u): UserItem {
            return new UserItem($u->id, $u->getProfileName(), $u->email, $u->getProfilePhoto());
        }, $teachersQuery->all());

        $teachers = array_filter($teachers, static function(UserItem $u) use ($user): bool {
            return $u->getId() !== $user->getId();
        });

        $accessForm = new TeacherAccessForm([
            'class_book_id' => $class_book_id,
        ]);

        return $this->controller->renderAjax('_teacher_access', [
            'formModel' => $accessForm,
            'classBookId' => $classBook->id,
            'accessJson' => Json::encode($access),
            'teachers' => $teachers,
        ]);
    }
}
