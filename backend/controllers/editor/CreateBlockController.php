<?php

namespace backend\controllers\editor;

use backend\components\story\reader\HtmlSlideReader;
use backend\components\story\Slide;
use backend\components\story\TextBlock;
use backend\components\story\writer\HTML\ParagraphBlockMarkup;
use backend\components\story\writer\HTMLWriter;
use backend\models\editor\TextForm;
use common\models\StorySlide;
use common\rbac\UserRoles;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class CreateBlockController extends Controller
{

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => ['POST'],
                ],
            ],
        ];
    }

    public function actionText()
    {
        $form = new TextForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

            $model = StorySlide::findSlide($form->slide_id);
            $slide = (new HtmlSlideReader($model->data))->load();

            /** @var TextBlock $block */
            $block = $slide->createBlock(TextBlock::class);
            $block->setText($form->text);
            $slide->addBlock($block);

            $model->data = (new HTMLWriter())->renderSlide($slide);
            $model->save(false, ['data']);

            return ['success' => true, 'html' => (new ParagraphBlockMarkup($block))->markup()];
        }
        return [];
    }

}