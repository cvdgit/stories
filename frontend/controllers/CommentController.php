<?php

namespace frontend\controllers;

use common\models\Comment;
use frontend\models\CommentForm;
use Yii;
use yii\web\Controller;

class CommentController extends Controller
{

    public function actionReply(int $id)
    {
        $model = Comment::findModel($id);

        $commentForm = new CommentForm($model->story_id);
        if ($commentForm->load(Yii::$app->request->post()) && $commentForm->validate()) {
            $commentForm->createComment(Yii::$app->user->id, $model->id);
            $commentForm->body = '';
        }

        $dataProvider = Comment::getCommentDataProvider($model->story_id);
        if (Yii::$app->request->isPjax) {
            return $this->renderAjax('/story/_comment_list', ['dataProvider' => $dataProvider]);
        }
    }

}