<?php

namespace ImAMadDev\youtubers\redeem;

use ImAMadDev\HCF;
use pocketmine\utils\Config;

class RedeemManager
{

    public static array $creators = [];

    public function __construct(
        public HCF $main){
        $this->init();
    }

    public function init() : void
    {
        if (!is_dir($this->main->getDataFolder() . "redeem")) @mkdir($this->main->getDataFolder() . "redeem/");
        foreach (glob($this->main->getDataFolder() . "redeem" . DIRECTORY_SEPARATOR . "*.yml") as $file) {
            $config = new Config($file, Config::YAML);
            $this->addRedeem(new RedeemCreator($config->get("creator"), $config->get("claims", 0)));
        }
    }

    public function addRedeem(RedeemCreator $creator) : void
    {
        self::$creators[$creator->getCreator()] = $creator;
    }

    public function getRedeem(string $creator) : null | RedeemCreator
    {
        return self::$creators[$creator] ?? null;
    }

    public function registerRedeem(string $creator) : void
    {
        $config = new Config($this->main->getDataFolder() . "redeem/" . $creator . ".yml", Config::YAML, ["creator" => $creator, "claims" => 0]);
        $config->save();
        $this->addRedeem(new RedeemCreator($creator));
    }
}