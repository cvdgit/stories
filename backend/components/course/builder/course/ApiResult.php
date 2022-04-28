<?php

namespace backend\components\course\builder\course;

class ApiResult
{

    private $items;
    private $links;

    public function __construct(array $items, array $links)
    {
        $this->items = $items;
        $this->links = $links;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }


}
