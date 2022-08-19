<?php

namespace backend\widgets\grid;

class UpdateButton extends Button
{

    public function __invoke(array $options = [])
    {
        return $this->createButton('pencil', 'Редактировать', $options);
    }
}
