<?php

namespace ImAMadDev\tile;

use pocketmine\block\tile\TileFactory;
use pocketmine\BLOCK\tile\Tile as PMTile;
use ReflectionException;
use ImAMadDev\HCF;

abstract class Tile extends PMTile {

    public static function init(){
        try{
           // self::registerTile(Beacon::class);
           // self::registerTile(ShulkerBox::class);
            //self::registerTile(Hopper::class);
            TileFactory::getInstance()->register(PotionGenerator::class, ["PotionGenerator", "minecraft:potion_generator"]);
            TileFactory::getInstance()->register(MonsterSpawner::class, ["MonsterSpawner", "minecraft:monster_spawner"]);
            //self::registerTile(MonsterSpawner::class);
            
            //self::registerTile(Jukebox::class);
        }catch(ReflectionException $e){
            HCF::getInstance()->getLogger()->error($e);
        }
    }
}
