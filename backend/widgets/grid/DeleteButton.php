<?php

namespace backend\widgets\grid;

class DeleteButton extends Button
{

    public function __invoke()
    {
        return $this->createButton('trash', 'Удалить', [
            'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?',
            'data-method' => 'post',
        ]);
    }

}
