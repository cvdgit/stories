<?php

declare(strict_types=1);

namespace backend\Testing\columns;

use yii\helpers\Url;

class ActionUrlCreator
{
    public static function updateUrl($model): string
    {
        $urlParam = ['test/update', 'id' => $model->id];
        if ($model->isVariant()) {
            $urlParam['id'] = $model->parent_id;
            $urlParam['#'] = $model->id;
        }
        return Url::to($urlParam);
    }

    public static function deleteUrl($model): string
    {
        $id = $model->id;
        if ($model->isVariant()) {
            $id = $model->parent_id;
        }
        return Url::to(['test/delete', 'id' => $id]);
    }

    public function __invoke(): \Closure
    {
        return static function ($action, $model, $key, $index): string {
            $url = '';
            if ($action === 'update') {
                $url = self::updateUrl($model);
            }
            if ($action === 'delete') {
                $url = self::deleteUrl($model);
            }
            return $url;
        };
    }
}
