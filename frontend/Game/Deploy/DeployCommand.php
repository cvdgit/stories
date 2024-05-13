<?php

declare(strict_types=1);

namespace frontend\Game\Deploy;

class DeployCommand
{
    /**
     * @var string
     */
    private $archFilePath;
    /**
     * @var string
     */
    private $folder;
    /**
     * @var string
     */
    private $buildName;

    public function __construct(string $archFilePath, string $folder, string $buildName)
    {
        $this->archFilePath = $archFilePath;
        $this->folder = $folder;
        $this->buildName = $buildName;
    }

    public function getArchFilePath(): string
    {
        return $this->archFilePath;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getBuildName(): string
    {
        return $this->buildName;
    }
}
