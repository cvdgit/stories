<?php

namespace backend\widgets\grid;

class UpdateButton extends Button
{

    public function __invoke()
    {
        return $this->createButton('pencil', 'Редактировать');
    }

}