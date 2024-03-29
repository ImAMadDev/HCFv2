<?php

namespace ImAMadDev\manager;

use ImAMadDev\entity\mobs\Blaze;
use ImAMadDev\entity\mobs\Cow;
use ImAMadDev\entity\mobs\Creeper;
use ImAMadDev\entity\mobs\Enderman;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;

use ImAMadDev\item\{EnderPearl, EnchantedBook, GoldenApple, GoldenAppleEnchanted, Fireworks, SplashPotion, EnderEye};
use ImAMadDev\HCF;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\item\PotionType;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\world\World;

class ItemManager {
	
	public static ?HCF $main = null;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->init();
	}
	
	public function init() {
        $if = ItemFactory::getInstance();
		$if->register(new EnderPearl(), true);
        //$if->register(new FishingRod(), true);
        foreach(PotionType::getAll() as $type){
            $typeId = PotionTypeIdMap::getInstance()->toId($type);
            $if->register(new Potion(new ItemIdentifier(ItemIds::POTION, $typeId), $type->getDisplayName() . " Potion", $type), true);
            $if->register(new SplashPotion(new ItemIdentifier(ItemIds::SPLASH_POTION, $typeId), $type->getDisplayName() . " Splash Potion", $type), true);
        }
        $if->register(new GoldenApple(new ItemIdentifier(ItemIds::GOLDEN_APPLE, 0), "Golden Apple"), true);
        $if->register(new GoldenAppleEnchanted(new ItemIdentifier(ItemIds::ENCHANTED_GOLDEN_APPLE,0), "Enchanted Golden Apple"), true);
        $if->register(new EnderEye(), true);
        $if->register(new EnchantedBook(), true);
        $if->register(new Fireworks(new ItemIdentifier(ItemIds::FIREWORKS, 0), "Fireworks"), true);
        $if->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::COW), "Cow Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Cow{
                return new Cow(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        });
        $if->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::ENDERMAN), "Enderman Spawn Egg") extends SpawnEgg{
            public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Enderman{
                return new Enderman(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        });
        $if->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::CREEPER), "Creeper Spawn Egg") extends SpawnEgg{
            public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Creeper{
                return new Creeper(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        });
        $if->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::BLAZE), "Blaze Spawn Egg") extends SpawnEgg{
            public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Blaze{
                return new Blaze(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        });
    }
}