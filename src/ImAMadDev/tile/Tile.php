<?php

namespace ImAMadDev\tile;

use pocketmine\tile\Tile as PMTile;
use ReflectionException;
use ImAMadDev\HCF;

abstract class Tile extends PMTile {
    /** @var string */
    public const
      BEACON = "Beacon", SHULKER_BOX = "ShulkerBox", HOPPER = "Hopper", JUKEBOX = "Jukebox", CAULDRON = "Cauldron";
    
    public static function init(){
        try{
           // self::registerTile(Beacon::class);
            self::registerTile(ShulkerBox::class);
            self::registerTile(Hopper::class);
            self::registerTile(PotionGenerator::class);
            self::registerTile(MonsterSpawner::class);
            
            //self::registerTile(Jukebox::class);
        }catch(ReflectionException $e){
            HCF::getInstance()->getLogger()->error($e); // stfu phpstorm
        }
    }
}
