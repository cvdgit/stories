<?php

namespace backend\widgets\grid;

class ViewButton extends Button
{

    public function __invoke(array $options = [])
    {
        return $this->createButton('eye-open', 'Просмотр', $options);
    }

}