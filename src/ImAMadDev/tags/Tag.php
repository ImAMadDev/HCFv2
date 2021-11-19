<?php

namespace ImAMadDev\tags;

use pocketmine\utils\TextFormat;

class Tag
{

    private string $name;
    private string $format;

    public function __construct(string $name, string $format)
    {
        $this->name = $name;
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return TextFormat::colorize($this->format);
    }

}