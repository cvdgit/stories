<?php


namespace common\widgets;


class Toastr extends BaseToastrWidget
{

    public function run()
    {
        if (in_array($this->type, $this->types)){
            return $this->view->registerJs("toastr.{$this->type}(\"{$this->message}\", \"{$this->title}\", {$this->options});");
        }
        return $this->view->registerJs("toastr.{$this->typeDefault}(\"{$this->message}\", \"{$this->title}\", {$this->options});");
    }

}