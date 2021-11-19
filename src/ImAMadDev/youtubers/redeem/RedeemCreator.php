<?php

namespace ImAMadDev\youtubers\redeem;

use ImAMadDev\HCF;
use pocketmine\utils\Config;

class RedeemCreator
{
    public function __construct(
        public string $creator,
        public int $claims = 0
    ){
    }

    /**
     * @return string
     */
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * @return int
     */
    public function getClaims(): int
    {
        return $this->claims;
    }

    /**
     * @param int $claims
     */
    public function addClaim(int $claims): void
    {
        $this->claims += $claims;
        $config = new Config(HCF::getInstance()->getDataFolder() . "redeem/" . $this->getCreator() . ".yml", Config::YAML);
        $config->set("claims", $this->getClaims());
        $config->save();
    }

}