<?php
use frontend\assets\MobileTestAsset;
MobileTestAsset::register($this);
$this->title = 'Mobile testing';
/** @var $model common\models\StoryTest */
?>
<div class="container" style="margin-bottom: 50px">
    <div class="row">
        <div class="col-md-12">
            <div id="mobile-testing" class="new-questions" data-test-id="<?= $model->id ?>"></div>
        </div>
    </div>
</div>