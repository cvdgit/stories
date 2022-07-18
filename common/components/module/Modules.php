<?php

namespace common\components\module;

use OutOfBoundsException;
use yii\base\Application;
use yii\base\Module;

final class Modules
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function definitions(): array
    {
        return $this->app->getModules();
    }

    public function load(string $id): Module
    {
        $module = $this->app->getModule($id);
        if ($module === null) {
            throw new OutOfBoundsException('Undefined module ' . $id);
        }
        return $module;
    }
}
