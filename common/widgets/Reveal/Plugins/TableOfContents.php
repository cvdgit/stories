<?php

declare(strict_types=1);

namespace common\widgets\Reveal\Plugins;

use common\widgets\Reveal\Dependency;

class TableOfContents extends AbstractPlugin implements PluginInterface
{
    public $configName = 'tableOfContentsConfig';
    public $edit = false;

    public function pluginConfig(): array
    {
        return [
            $this->configName => [
                'edit' => $this->edit,
            ],
        ];
    }

    public function pluginCssFiles(): array
    {
        return [
            (new Dependency('/js/player/plugins/table-of-contents.css'))->src,
        ];
    }

    public function dependencies(): array
    {
        return [
            new Dependency('/js/player/plugins/table-of-contents.js'),
        ];
    }
}
