<?php

namespace backend\controllers\user;

use backend\components\BaseController;
use common\models\User;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Response;

class AutocompleteController extends BaseController
{

    public function actions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::actions();
    }

    public function actionSelect(string $query)
    {
        return (new Query())
            ->select(['username AS title', 'email', 'id', new Expression("'/img/no_avatar.png' AS cover")])
            ->from(User::tableName())
            ->where([
                'or',
                ['like', 'username', $query],
                ['like', 'email', $query],
            ])
            ->andWhere('status = :status', [':status' => User::STATUS_ACTIVE])
            ->orderBy(['username' => SORT_ASC])
            ->limit(30)
            ->all();
    }
}
