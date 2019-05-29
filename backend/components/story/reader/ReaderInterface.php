<?php


namespace backend\components\story\reader;


use backend\components\story\Story;

interface ReaderInterface
{

    public function load(): Story;

}