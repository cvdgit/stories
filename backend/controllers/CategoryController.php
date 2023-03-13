<?php

namespace backend\controllers;

use backend\components\BaseController;
use backend\models\category\CreateCategoryForm;
use backend\models\category\CreateTreeForm;
use backend\models\category\UpdateCategoryForm;
use backend\models\StorySearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Category;
use common\rbac\UserRoles;
use yii\web\NotFoundHttpException;
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

    /**
     * @throws NotFoundHttpException
     */
    private function findRootCategoryByTree(int $tree): Category
    {
        if (($model = Category::findRootByTree($tree)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Дерево категорий не найдено');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex(int $tree = 0): string
    {
/*        $root = Category::findRootByTree($tree);
        if ($root === null) {
            $root = new Category([
                'name' => 'new',
                'tree' => 0,
            ]);
        }*/
        $rootCategory = $this->findRootCategoryByTree($tree);
        return $this->render('tree', [
            'rootCategory' => $rootCategory,
            'treeItems' => Category::getTreeItems($rootCategory->tree),
            'data' => Category::categoryArray2($rootCategory->id),
        ]);
    }

    public function actionCreate(int $tree = 0, int $parent_id = null)
    {
        $treeCategory = $this->findRootCategoryByTree($tree);

        $model = new CreateCategoryForm();
        $model->tree = $treeCategory->tree;

        if (!empty($parent_id)) {
            /** @var Category $categoryModel */
            $parentCategoryModel = $this->findModel(Category::class, $parent_id);
            $model->parent = $parentCategoryModel->id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->createCategory();
            Yii::$app->session->setFlash('success', 'Категория успешно создана');
            return $this->redirect(['index', 'tree' => $model->tree]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdateAjax($id)
    {
        /** @var Category $model */
        $categoryModel = $this->findModel(Category::class, $id);

        $model = new UpdateCategoryForm($categoryModel);
        $model->tree = $categoryModel->tree;

        if ($model->load(Yii::$app->request->post())) {
            $model->updateCategory();
            Yii::$app->session->setFlash('success', 'Категория успешно обновлена');
            return $this->redirect(['index', 'tree' => $model->tree]);
        }

        return $this->renderAjax('_form', [
            'category' => $categoryModel,
            'model' => $model,
            'treeItems' => Category::getMoveTreeItems($categoryModel->id, $model->tree),
        ]);
    }

    public function actionMove(int $item, string $action, int $second)
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
        if (Yii::$app->request->isAjax) {
            return $itemModel->save();
        }
        return $this->redirect(['index', 'tree' => $secondModel->tree]);
    }

    public function actionDelete($id)
    {
        /** @var Category $categoryModel */
        $categoryModel = $this->findModel(Category::class, $id);
        $categoryModel->delete();
        Yii::$app->session->setFlash('success', 'Категория успешно удалена');
        return $this->redirect(['index', 'tree' => $categoryModel->tree]);
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
            $searchModel->defaultSortOrder = !empty($categoryModel->sort_order) ? $categoryModel->sort_order : SORT_ASC;
        }
        $searchModel->category_id = implode(',', $categoryModel->subCategories());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->renderAjax('_category_stories', [
            'models' => $dataProvider->getModels(),
        ]);
    }

    public function actionCreateRoot()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $form = new CreateTreeForm();
        if ($form->load(Yii::$app->request->post())) {
            $form->createTree();
            Yii::$app->session->setFlash('success', 'Новое дерево успешно создано');
            return ['success' => true];
        }
        return ['success' => false];
    }
}
