<?php

namespace ImAMadDev\utils;

use ImAMadDev\HCF;
use pocketmine\utils\Config;
use pocketmine\utils\Filesystem;

class ServerInformation
{

    public function __construct(
        private HCF $HCF
    )
    {
        $this->loadData();
    }

    /**
     * @return HCF
     */
    public function getHCF(): HCF
    {
        return $this->HCF;
    }

    public function loadData() : void
    {
        if (!is_file($this->getHCF()->getDataFolder() . "Configuration.yml")){
            $contents = yaml_emit($this->getDefaultConfiguration(), YAML_UTF8_ENCODING);
            Filesystem::safeFilePutContents($this->getHCF()->getDataFolder() . "Configuration.yml", $contents);
        } else {
            $contents = yaml_parse(Config::fixYAMLIndexes(file_get_contents($this->getHCF()->getDataFolder() . "Configuration.yml")));

        }
    }

    private function getDefaultConfiguration() : array
    {
        return [
            'default_spawn' => '0:80:0',
            'default_world' => 'world',
            'nether_spawn' => '0:80:0',
            'nether_wold' => 'Nether',
            'end_spawn' => '0:80:0',
            'end_world' => 'End',
            'Donor_rank_name' => 'Waffle',
            'max_protection' => 1,
            'vip_protection' => 2,
            'koth_protection' => 3,
            'scoreboard_title' => '&gWaffle | HCF'
        ];
    }

}