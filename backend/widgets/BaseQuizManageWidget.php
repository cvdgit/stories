<?php

declare(strict_types=1);

namespace backend\widgets;

use yii\base\Widget;

abstract class BaseQuizManageWidget extends Widget
{
    /** @var int|null */
    public $currentModelId;

    /** @var string */
    public $renderData;

    protected $createItemTitle = 'Создать';

    abstract public function getItemsData(): array;

    protected function createItem(string $label, $url, bool $active): array
    {
        return [
            'label' => $label,
            'url' => $url,
            'active' => $active,
        ];
    }

    abstract public function itemCallback($item): array;

    protected function makeNavItems(): array
    {
        $items = [];
        if ($this->currentModelId === null) {
            $items[] = $this->createItem($this->createItemTitle,'#',true);
        }
        return array_merge($items, array_map([$this, 'itemCallback'], $this->getItemsData()));
    }
}
