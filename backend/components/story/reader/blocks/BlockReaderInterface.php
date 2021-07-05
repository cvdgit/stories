<?php

namespace backend\components\story\reader\blocks;

use backend\components\story\AbstractBlock;

interface BlockReaderInterface
{

    public function createBlock(): AbstractBlock;

}