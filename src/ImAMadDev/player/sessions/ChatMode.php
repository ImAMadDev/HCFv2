<?php

namespace ImAMadDev\player\sessions;

class ChatMode
{

    public function __construct(
        private int $mode){}

    /**
     * @return int
     */
    public function get(): int
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function set(int $mode): void
    {
        $this->mode = $mode;
    }

}