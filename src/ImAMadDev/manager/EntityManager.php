<?php

namespace ImAMadDev\manager;

use ImAMadDev\item\Fireworks;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\entity\projectile\{FishingHook, Switcher, FireworksRocket, SplashPotion, EnderPearl};
use ImAMadDev\entity\CombatLogger;
use ImAMadDev\npc\types\{BlackMarket, BlockMarket, LegendaryMarket, TopOne, TopThree, TopTwo};
use ImAMadDev\entity\mobs\{Enderman, Cow, Creeper, Blaze};
use ImAMadDev\HCF;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Skin;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;

class EntityManager {
	
	public static HCF $main;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->init();
	}
	public function init() {
        $skin = new Skin("NPCEntity", base64_encode(str_repeat(random_bytes(5) . "\xff", 2048)));
        $factory = EntityFactory::getInstance();
        $factory->register(FishingHook::class, function(World $world, CompoundTag $nbt) : FishingHook{
            return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['FishingHook', 'minecraft:fishinghook'], EntityLegacyIds::FISHING_HOOK);
        $factory->register(SplashPotion::class, function(World $world, CompoundTag $nbt) : SplashPotion{
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort("PotionId", PotionTypeIds::WATER));
            if($potionType === null){
                throw new \UnexpectedValueException("No such potion type");
            }
            return new SplashPotion(EntityDataHelper::parseLocation($nbt, $world), null, $potionType, $nbt);
        }, ['ThrownPotion', 'minecraft:potion', 'thrownpotion'], EntityLegacyIds::SPLASH_POTION);
        $factory->register(EnderPearl::class, function(World $world, CompoundTag $nbt) : EnderPearl{
            return new EnderPearl(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityLegacyIds::ENDER_PEARL);
        $factory->register(CombatLogger::class, function(World $world, CompoundTag $nbt) use($skin): CombatLogger{
            return new CombatLogger(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['CombatLogger', 'minecraft:combat_logger'], EntityLegacyIds::VILLAGER);
        $factory->register(BlackMarket::class, function(World $world, CompoundTag $nbt) use($skin): BlackMarket{
            return new BlackMarket(EntityDataHelper::parseLocation($nbt, $world), $skin, $nbt);
        }, ['BlackMarket', 'minecraft:black_market'], EntityLegacyIds::NPC);
        $factory->register(LegendaryMarket::class, function(World $world, CompoundTag $nbt) use($skin): LegendaryMarket{
            return new LegendaryMarket(EntityDataHelper::parseLocation($nbt, $world), $skin, $nbt);
        }, ['LegendaryMarket', 'minecraft:legendary_market'], EntityLegacyIds::NPC);
        $factory->register(TopOne::class, function(World $world, CompoundTag $nbt) use($skin): TopOne{
            return new TopOne(EntityDataHelper::parseLocation($nbt, $world), $skin, $nbt);
        }, ['TopOne', 'minecraft:top_one'], EntityLegacyIds::NPC);
        $factory->register(TopTwo::class, function(World $world, CompoundTag $nbt) use($skin): TopTwo{
            return new TopTwo(EntityDataHelper::parseLocation($nbt, $world), $skin, $nbt);
        }, ['TopTwo', 'minecraft:top_two'], EntityLegacyIds::NPC);
        $factory->register(TopThree::class, function(World $world, CompoundTag $nbt) use($skin): TopThree{
            return new TopThree(EntityDataHelper::parseLocation($nbt, $world), $skin, $nbt);
        }, ['TopThree', 'minecraft:top_three'], EntityLegacyIds::NPC);
        $factory->register(BlockMarket::class, function(World $world, CompoundTag $nbt) use($skin): BlockMarket{
            return new BlockMarket(EntityDataHelper::parseLocation($nbt, $world), $skin, $nbt);
        }, ['BlockMarket', 'minecraft:block_market'], EntityLegacyIds::NPC);
        $factory->register(Switcher::class, function (World $world, CompoundTag $nbt) : Switcher {
            return new Switcher(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['Switcher', 'minecraft:switcher']);
       /*$factory->register(FireworksRocket::class, function(World $world, CompoundTag $nbt) : FireworksRocket{
            return new FireworksRocket(EntityDataHelper::parseLocation($nbt, $world), ItemFactory::getInstance()->get(ItemIds::FIREWORKS, 0));
        }, ['FireworksRocket', EntityIds::FIREWORKS_ROCKET], EntityLegacyIds::FIREWORKS_ROCKET);
		Entity::registerEntity(Switcher::class, true, ['Switcher']);
		Entity::registerEntity(Enderman::class, true, ['Enderman']);
		Entity::registerEntity(Cow::class, true, ['Cow']);
		Entity::registerEntity(Creeper::class, true, ['Creeper']);
		Entity::registerEntity(Blaze::class, true, ['Blaze']);*/
	}
	
	public static function spawn(HCFPlayer $player, string $name) : void {
		switch($name) {
			case "black_market":
				$entity = new BlackMarket($player->getLocation(), $player->getSkin());
				$entity->setNameTag("BlackMarket");
				$entity->spawnToAll();
				break;
			case "block_market":
				$entity = new BlockMarket($player->getLocation(), $player->getSkin());
				$entity->setNameTag("BlockMarket");
				$entity->spawnToAll();
				break;
			case "legendary_market":
                $entity = new LegendaryMarket($player->getLocation(), $player->getSkin());
				$entity->setNameTag("LegendaryMarket");
				$entity->spawnToAll();
				break;
            case "top_one":
                $entity = new TopOne($player->getLocation(), $player->getSkin());
                $entity->setNameTag("TopOne");
                $entity->spawnToAll();
                break;
            case "top_two":
                $entity = new TopTwo($player->getLocation(), $player->getSkin());
                $entity->setNameTag("TopTwo");
                $entity->spawnToAll();
                break;
            case "top_three":
                $entity = new TopThree($player->getLocation(), $player->getSkin());
                $entity->setNameTag("TopThree");
                $entity->spawnToAll();
                break;
			default:
				$player->sendMessage("Entidad $name no existe: black_market, block_market, legendary_market");
				break;
		}
	}
	
	public static function despawn(HCFPlayer $player, string $name) : void {
		switch($name) {
			case "black_market":
				foreach($player->getServer()->getWorldManager()->getWorlds() as $level) {
					foreach($level->getEntities() as $entity) {
						if($entity instanceof BlackMarket) {
							$entity->flagForDespawn();
						}
					}
				}
				break;
			case "block_market":
                foreach($player->getServer()->getWorldManager()->getWorlds() as $level) {
					foreach($level->getEntities() as $entity) {
						if($entity instanceof BlockMarket) {
							$entity->flagForDespawn();
						}
					}
				}
				break;
			case "legendary_market":
                foreach($player->getServer()->getWorldManager()->getWorlds() as $level) {
					foreach($level->getEntities() as $entity) {
						if($entity instanceof LegendaryMarket) {
							$entity->flagForDespawn();
						}
					}
				}
				break;
            case "top_one":
                foreach($player->getServer()->getWorldManager()->getWorlds() as $level) {
                    foreach($level->getEntities() as $entity) {
                        if($entity instanceof TopOne) {
                            $entity->flagForDespawn();
                        }
                    }
                }
                break;
            case "top_two":
                foreach($player->getServer()->getWorldManager()->getWorlds() as $level) {
                    foreach($level->getEntities() as $entity) {
                        if($entity instanceof TopTwo) {
                            $entity->flagForDespawn();
                        }
                    }
                }
                break;
            case "top_three":
                foreach($player->getServer()->getWorldManager()->getWorlds() as $level) {
                    foreach($level->getEntities() as $entity) {
                        if($entity instanceof TopThree) {
                            $entity->flagForDespawn();
                        }
                    }
                }
                break;
			default:
				$player->sendMessage("Entidad $name no existe: black_market, block_market, legendary_market");
				break;
		}
	}
}