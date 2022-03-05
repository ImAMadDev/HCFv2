<?php

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\{Block, BlockFactory, BlockLegacyIds, utils\DyeColor, VanillaBlocks, Air};
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

    public function createBase(HCFPlayer $player)
    {
        $world = $player->getPosition()->getWorld();
        $size = $this->getSize();
        $middle = $size / 2;
        $x = round($this->getCenter()->getFloorX());
        $y = round($this->getCenter()->getFloorY());
        $z = round($this->getCenter()->getFloorZ());
        for ($i = 0; $i <= $size; $i++){
            for ($k = 0; $k <= $size; $k++){
                for ($j = -1; $j <= 4; $j++) {
                    if ($j == -1 or $j == 4){
                        $world->setBlockAt(($x+$i), ($j+$y), ($z+$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                        $world->setBlockAt(($x-$i), ($j+$y), ($z-$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                        $world->setBlockAt(($x+$i), ($j+$y), ($z-$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                        $world->setBlockAt(($x-$i), ($j+$y), ($z+$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                    } else {
                        if($i == $size or $k == $size) {
                            if ($j == 0 or $j == 2 or $j == 3){
                                $world->setBlockAt(($x+$i), ($j+$y), ($z+$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                                $world->setBlockAt(($x-$i), ($j+$y), ($z-$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                                $world->setBlockAt(($x+$i), ($j+$y), ($z-$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                                $world->setBlockAt(($x-$i), ($j+$y), ($z+$k), VanillaBlocks::WOOL()->setColor(DyeColor::GREEN()));
                            } else {
                                $world->setBlockAt(($x+$i), ($j+$y), ($z+$k), VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                                $world->setBlockAt(($x-$i), ($j+$y), ($z-$k), VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                                $world->setBlockAt(($x+$i), ($j+$y), ($z-$k), VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                                $world->setBlockAt(($x-$i), ($j+$y), ($z+$k), VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN()));
                            }
                 		}
                    }
                }
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