<?php

namespace backend\components;

interface StoryRenderableInterface
{
	public function render(): string;
	public function getElements(): array;
}
