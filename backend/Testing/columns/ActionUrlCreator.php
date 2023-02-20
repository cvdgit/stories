<?php

declare(strict_types=1);

namespace backend\Testing\columns;

use yii\helpers\Url;

class ActionUrlCreator
{
    public function __invoke(): \Closure
    {
        return static function($action, $model, $key, $index) {
            $url = '';
            if ($action === 'update') {
                $urlParam = ['test/update', 'id' => $model->id];
                if ($model->isVariant()) {
                    $urlParam['id'] = $model->parent_id;
                    $urlParam['#'] = $model->id;
                }
                $url = Url::to($urlParam);
            }
            if ($action === 'delete') {
                $id = $model->id;
                if ($model->isVariant()) {
                    $id = $model->parent_id;
                }
                $url = Url::to(['test/delete', 'id' => $id]);
            }
            return $url;
        };
    }
}
