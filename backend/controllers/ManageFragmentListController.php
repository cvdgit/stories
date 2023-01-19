<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\forms\FragmentListForm;
use backend\forms\FragmentListItemForm;
use common\rbac\UserRoles;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\web\Request;

class ManageFragmentListController extends Controller
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserRoles::PERMISSION_MANAGE_TEST],
                    ],
                ],
            ],
        ]);
    }

    public function actionItems(int $id): array
    {
        return (new Query())
            ->select('*')
            ->from('fragment_list_item')
            ->where(['fragment_list_id' => $id])
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }

    public function actionCreate(int $id): array
    {
        Yii::$app->db->createCommand()
            ->insert('fragment_list_item', [
                'fragment_list_id' => $id,
                'name' => 'Новый элемент',
            ])
            ->execute();
        $itemId = Yii::$app->db->lastInsertID;
        return [
            'id' => $itemId,
            'name' => 'Новый элемент',
        ];
    }

    public function actionChangeItemName(int $id, Request $request): array
    {
        $fragmentItemForm = new FragmentListItemForm();
        $fragmentItemForm->attributes = (new Query())
            ->select('*')
            ->from('fragment_list_item')
            ->where(['id' => $id])
            ->one();
        if ($fragmentItemForm->load($request->post()) && $fragmentItemForm->validate()) {

            Yii::$app->db->createCommand()
                ->update('fragment_list_item', ['name' => $fragmentItemForm->name], ['id' => $id])
                ->execute();

            return ['success' => true];
        }
        return ['success' => false, 'name' => $fragmentItemForm->name];
    }

    public function actionChangeListName(int $id, Request $request): array
    {
        $fragmentListForm = new FragmentListForm();
        $fragmentListForm->attributes = (new Query())
            ->select('*')
            ->from('fragment_list')
            ->where(['id' => $id])
            ->one();
        if ($fragmentListForm->load($request->post()) && $fragmentListForm->validate()) {

            Yii::$app->db->createCommand()
                ->update('fragment_list', ['name' => $fragmentListForm->name], ['id' => $id])
                ->execute();

            return ['success' => true];
        }
        return ['success' => false, 'name' => $fragmentListForm->name];
    }

    public function actionRemoveList(int $id): array
    {
        Yii::$app->db->createCommand()
            ->delete('fragment_list_item', ['fragment_list_id' => $id])
            ->execute();
        Yii::$app->db->createCommand()
            ->delete('fragment_list_tag', ['fragment_list_id' => $id])
            ->execute();
        Yii::$app->db->createCommand()
            ->delete('fragment_list_testing', ['fragment_list_id' => $id])
            ->execute();
        Yii::$app->db->createCommand()
            ->delete('fragment_list', ['id' => $id])
            ->execute();
        return ['success' => true];
    }
}
