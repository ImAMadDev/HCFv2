<?php

namespace ImAMadDev\player\modules;

use pocketmine\player\Player;
use pocketmine\Server;

class FactionRank
{

    private string|null $rank = null;

    public function __construct(
        private string $name
    ){}

    /**
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerByPrefix($this->getName());
    }

    /**
     * @return string|null
     */
    public function get(): ?string
    {
        return $this->rank;
    }

    /**
     * @param string|null $rank
     */
    public function set(?string $rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }


}