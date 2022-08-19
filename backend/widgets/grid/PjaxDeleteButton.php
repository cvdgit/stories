<?php

namespace backend\widgets\grid;

class PjaxDeleteButton extends Button
{
    public function __toString()
    {
        return $this->createButton('trash', 'Удалить');
    }
}
