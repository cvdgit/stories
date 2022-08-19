<?php

namespace backend\controllers\story;

use backend\components\BaseController;
use common\models\Story;
use common\models\story\StoryStatus;
use Yii;
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
            ->select(['title', 'id', "IF(cover IS NULL, '/img/story-1.jpg', CONCAT('/slides_cover/list/', cover)) AS cover"])
            ->from(Story::tableName())
            ->where(['like', 'title', $query])
            ->andWhere('status <> :status', [':status' => StoryStatus::TASK])
            ->orderBy(['title' => SORT_ASC])
            ->limit(30)
            ->all();
    }

    public function actionSelectAll(string $query)
    {
        return (new Query())
            ->select(['title', 'id', "IF(cover IS NULL, '/img/story-1.jpg', CONCAT('/slides_cover/list/', cover)) AS cover"])
            ->from(Story::tableName())
            ->where(['like', 'title', $query])
            ->orderBy(['title' => SORT_ASC])
            ->limit(30)
            ->all();
    }

    public function actionSelectPublished(string $query): array
    {
        return (new Query())
            ->select(['title', 'id', "IF(cover IS NULL, '/img/story-1.jpg', CONCAT('/slides_cover/list/', cover)) AS cover"])
            ->from(Story::tableName())
            ->where(['like', 'title', $query])
            ->andWhere('status = :status', [':status' => StoryStatus::PUBLISHED])
            ->orderBy(['title' => SORT_ASC])
            ->limit(30)
            ->all();
    }
}
