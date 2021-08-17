<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\StorySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Category;
use common\rbac\UserRoles;
use yii\web\Response;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_CATEGORIES],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex($id = 1)
    {
        $categoryModel = $this->findModel(Category::class, $id);
        return $this->render('tree', [
            'data' => $categoryModel->categoryArray2(),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Category();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->parentNode === null) {
                $model->makeRoot();
            }
            else {
                $parent = Category::findOne($model->parentNode);
                $model->appendTo($parent);
            }
            if ($model->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel(Category::class, $id);
        $parent = $model->parents(1)->one();
        if ($parent !== null) {
            $model->parentNode = $parent->id;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAjax($id)
    {
        $model = $this->findModel(Category::class, $id);
        $parent = $model->parents(1)->one();
        if ($parent !== null) {
            $model->parentNode = $parent->id;
        }
        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    public function actionMove($item, $action, $second)
    {
        $itemModel = $this->findModel(Category::class, $item);
        $secondModel = $this->findModel(Category::class, $second);
        switch ($action) {
            case 'after':
                $itemModel->insertAfter($secondModel);
                break;
            case 'before':
                $itemModel->insertBefore($secondModel);
                break;
            case 'over':
                $itemModel->appendTo($secondModel);
                break;
        }
        return $itemModel->save();
    }

    public function actionDelete($id)
    {
        $this->findModel(Category::class, $id)->delete();
        return $this->redirect(['index']);
    }

    public function actionList($query)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $models = Category::findAllByName($query);
        $items = [];
        foreach ($models as $model) {
            $items[] = ['name' => $model->name];
        }
        return $items;
    }


    public function actionStories(int $category_id)
    {
        $categoryModel = $this->findModel(Category::class, $category_id);
        $searchModel = new StorySearch();
        $searchModel->setPageSize(false);
        if (!empty($categoryModel->sort_field)) {
            $searchModel->defaultSortField = $categoryModel->sort_field;
            $searchModel->defaultSortOrder = !empty($categoryModel->sort_order) ? $categoryModel->sort_order : SORT_ASC;;
        }
        $searchModel->category_id = implode(',', $categoryModel->subCategories());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->renderAjax('_category_stories', [
            'models' => $dataProvider->getModels(),
        ]);
    }
}
