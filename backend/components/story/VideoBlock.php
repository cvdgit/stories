<?php


namespace backend\components\story;


class VideoBlock extends AbstractBlock
{

    /** @var string */
    protected $video_id;

    /** @var int */
    protected $seek_to;

    /** @var int */
    protected $duration;

    /** @var bool */
    protected $mute;

    public function update($form)
    {
        $this->setSizeAndPosition($form->width, $form->height, $form->left, $form->top);
        $this->video_id = $form->video_id;
        $this->seek_to = $form->seek_to;
        $this->duration = $form->duration;
        $this->mute = $form->mute;
    }

    public function create()
    {
        $block = new self();
        $block->setWidth('973px');
        $block->setHeight('720px');
        $block->setLeft(0);
        $block->setTop(0);
        return $block;
    }

    /**
     * @return string
     */
    public function getVideoId()
    {
        return $this->video_id;
    }

    /**
     * @param string $video_id
     */
    public function setVideoId($video_id): void
    {
        $this->video_id = $video_id;
    }

    /**
     * @return int
     */
    public function getSeekTo()
    {
        return $this->seek_to;
    }

    /**
     * @param int $seek_to
     */
    public function setSeekTo($seek_to): void
    {
        $this->seek_to = $seek_to;
    }

    public function getValues(): array
    {
        return array_merge([
            'video_id' => $this->video_id,
            'seek_to' => $this->seek_to,
            'duration' => $this->duration,
            'mute' => $this->mute,
        ], parent::getValues());
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return bool
     */
    public function isMute()
    {
        return $this->mute;
    }

    /**
     * @param bool $mute
     */
    public function setMute($mute): void
    {
        $this->mute = $mute;
    }

}