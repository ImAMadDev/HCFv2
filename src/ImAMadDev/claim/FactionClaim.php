<?php

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\{BlockFactory, BlockLegacyIds, utils\DyeColor, VanillaBlocks, Air};
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\world\{World, Position};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\koth\KOTHArena;
use ImAMadDev\manager\EOTWManager;
use ImAMadDev\faction\Faction;

class FactionClaim extends EditableClaim {
	
	public function __construct(HCF $main, array $data) {
		parent::__construct($main, $data);
	}
	
	public function createBase(HCFPlayer $player) : void
    {
    	$world = $player->getPosition()->getWorld();
    	$size = $this->getSize() / 2;
    	$x = $this->getCenter()->x;
        $y = $this->getCenter()->y;
        $z = $this->getCenter()->z;
        for ($i = 1; $i <= $size; $i++){
        	for ($k = 1; $k <= $size; $k++){
         	   for ($j = 1; $j <= 4; $j++) {
              	  if ($j == 1 or $j == 4){
                 	   $world->setBlockAt(($x+$i), ($j+$y), ($z+$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                		 $world->setBlockAt(($x-$i), ($j+$y), ($z-$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                 	  // $world->setBlockAt($x, $y, 45, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
             	   } else {
                 	   $world->setBlockAt(($x+$i), ($j+$y), ($z+$k), VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                  	  //$world->setBlockAt($x, $y, 45, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
               	 }
       	     }
            }
        }
        for ($z = 35; $z < 45; $z++) {
            for ($y = 100; $y < 103; $y++) {
                if ($y == 100 or $y == 104){
                    $world->setBlockAt(14, $y, $z, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                    $world->setBlockAt(3, $y, $z, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                } else {
                    $world->setBlockAt(14, $y, $z, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                    $world->setBlockAt(3, $y, $z, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                }
            }
        }
        for ($x = 3; $x < 15; $x++){
            for ($z = 35; $z < 45; $z++){
                $world->setBlockAt($x, 103, $z, VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
            }
        }
    }
    
    #[Pure] public function getCenter(): Vector3
    {
    	$p1 = $this->getProperties()->getPosition1();
    	$p2 = $this->getProperties()->getPosition2();
		return new Vector3(round(($p1->getX() + $p2->getX()) / 2), 0, round(($p1->getZ() + $p2->getZ()) / 2));
    }
}