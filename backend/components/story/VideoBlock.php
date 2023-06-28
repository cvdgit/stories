<?php

declare(strict_types=1);

namespace backend\components\story;

use backend\models\editor\VideoForm;
use backend\models\video\VideoSource;

class VideoBlock extends AbstractBlock
{
    protected $type = AbstractBlock::TYPE_VIDEO;

    public const DEFAULT_SPEED = 1;
    public const DEFAULT_VOLUME = 0.8;

    private $video_id;
    private $seek_to;
    private $duration;
    private $mute = false;
    private $speed;
    private $volume;
    private $to_next_slide = false;
    private $source;
    private $content;
    private $show_captions = false;
    private $captions_url;

    /**
     * @param VideoForm $form
     * @return void
     */
    public function update($form): void
    {
        $this->setVideoId($form->video_id);
        $this->setSeekTo((float) $form->seek_to);
        $this->setDuration((float) $form->duration);
        $this->setMute((int) $form->mute === 1);
        $this->setSpeed((float) $form->speed);
        $this->setVolume((float) $form->volume);
        $this->setToNextSlide((int) $form->to_next_slide === 1);
        $this->setShowCaptions((int) $form->show_captions === 1);
    }

    /**
     * @return self
     */
    public function create()
    {
        $block = new self();
        $block->setWidth('973px');
        $block->setHeight('720px');
        $block->setLeft(0);
        $block->setTop(0);
        $block->setDuration(0);
        $block->setSource(VideoSource::YOUTUBE);
        $block->setType(AbstractBlock::TYPE_VIDEO);
        $block->setShowCaptions(false);
        $block->setMute(false);
        return $block;
    }

    public function setSource(int $value): void
    {
        $this->source = $value;
    }

    public function getSource(): int
    {
        return $this->source;
    }

    public function getVideoId(): ?string
    {
        return $this->video_id;
    }

    public function setVideoId(string $video_id): void
    {
        $this->video_id = $video_id;
    }

    public function getSeekTo(): ?float
    {
        return $this->seek_to;
    }

    public function setSeekTo(float $seek_to): void
    {
        $this->seek_to = $seek_to;
    }

    public function getValues(): array
    {
        return array_merge([
            'video_id' => $this->getVideoId(),
            'seek_to' => $this->getSeekTo(),
            'duration' => $this->getDuration(),
            'mute' => $this->isMute(),
            'speed' => $this->getSpeed(),
            'volume' => $this->getVolume(),
            'to_next_slide' => $this->isToNextSlide(),
            'source' => $this->getSource(),
            'show_captions' => $this->isShowCaptions(),
        ], parent::getValues());
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): void
    {
        $this->duration = $duration;
    }

    public function isMute(): bool
    {
        return $this->mute;
    }

    public function setMute(bool $mute = true): void
    {
        $this->mute = $mute;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): void
    {
        $this->speed = $speed;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): void
    {
        $this->volume = $volume;
    }

    public function isToNextSlide(): bool
    {
        return $this->to_next_slide;
    }

    public function setToNextSlide(bool $value): void
    {
        $this->to_next_slide = $value;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isShowCaptions(): bool
    {
        return $this->show_captions;
    }

    public function setShowCaptions(bool $showCaptions = true): void
    {
        $this->show_captions = $showCaptions;
    }

    public function setCaptionsUrl(string $url = null): void
    {
        $this->captions_url = $url;
    }

    public function getCaptionsUrl(): ?string
    {
        return $this->captions_url;
    }
}
