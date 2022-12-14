<?php

declare(strict_types=1);

use frontend\assets\MobileTestAsset;

/**
 * @var string $content
 * @var $manager backend\components\book\SlideBlocks
 */

MobileTestAsset::register($this);
?>
<section>
    <?= $content; ?>
</section>
<hr>
