<?php

declare(strict_types=1);

use common\models\SlideVideo;
use frontend\assets\VideoAsset;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var SlideVideo $video
 */

VideoAsset::register($this);
?>
<video id="player" playsinline controls>
    <source src="<?= $video->getUploadedFileUrl('video_id'); ?>" type="video/mp4" />
    <?php if ($video->haveCaptions()): ?>
        <track
            kind="captions"
            src="<?= Url::to(['/video/file/captions', 'id' => $video->id]); ?>"
            srclang="en"
            label="English"
            default
        >
    <?php endif; ?>
</video>
