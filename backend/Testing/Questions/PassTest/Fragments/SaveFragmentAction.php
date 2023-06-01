<?php

declare(strict_types=1);

namespace backend\Testing\Questions\PassTest\Fragments;

use yii\base\Action;
use yii\base\Model;
use yii\web\Request;
use yii\web\Response;
use yii\web\User as WebUser;

class SaveFragmentAction extends Action
{
    private $handler;

    public function __construct($id, $controller, SaveFragmentHandler $handler, $config = [])
    {
        parent::__construct($id, $controller, $config);
        $this->handler = $handler;
    }

    public function run(Request $request, Response $response, WebUser $user): array
    {
        $response->format = Response::FORMAT_JSON;

        $fragmentForm = new FragmentListForm([
            'name' => $request->post('id'),
        ]);

        if (!$fragmentForm->validate()) {
            return ['success' => false, 'message' => 'fragment not valid'];
        }

        $items = [];
        foreach ($request->post('items') as $i => $rawModel) {
            $items[$i] = new FragmentListItemForm();
        }

        if (Model::loadMultiple($items, $request->post('items'), '') && Model::validateMultiple($items)) {
            try {
                $this->handler->handle(new SaveFragmentCommand($user->getId(), $fragmentForm->name, array_map(static function(FragmentListItemForm $item): array {
                    return ['name' => $item->title];
                }, $items)));
                return ['success' => true, 'message' => 'Список успешно создан'];
            }
            catch (\Exception $ex) {
                return ['success' => false, 'message' => $ex->getMessage()];
            }
        }

        return ['success' => false, 'message' => 'no data'];
    }
}
