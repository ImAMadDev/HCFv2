<?php

namespace ImAMadDev\manager;

use Exception;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\entity\projectile\{FishingHook, Switcher, FireworksRocket, SplashPotion, EnderPearl, EggPorts};
use ImAMadDev\entity\CombatLogger;
use ImAMadDev\npc\types\{BlackMarket, BlockMarket, LegendaryMarket, TopOne, TopThree, TopTwo};
use ImAMadDev\entity\mobs\{Enderman, Protector, Cow, Creeper, Blaze};
use ImAMadDev\HCF;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;
use UnexpectedValueException;

class EntityManager {
	
	public static HCF $main;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->init();
	}

    /**
     * @throws Exception
     */
    public function init() {
        $skin = new Skin("NPCEntity", base64_encode(str_repeat(random_bytes(5) . "\xff", 2048)));
        $factory = EntityFactory::getInstance();
        $factory->register(FishingHook::class, function(World $world, CompoundTag $nbt) : FishingHook{
            return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['FishingHook', 'minecraft:fishinghook'], EntityLegacyIds::FISHING_HOOK);
        $factory->register(SplashPotion::class, function(World $world, CompoundTag $nbt) : SplashPotion{
            $potionType = PotionTypeIdMap::getInstance()->fromId($nbt->getShort("PotionId", PotionTypeIds::WATER));
            if($potionType === null){
                throw new UnexpectedValueException("No such potion type");
            }
            return new SplashPotion(EntityDataHelper::parseLocation($nbt, $world), null, $potionType, $nbt);
        }, ['ThrownPotion', EntityIds::SPLASH_POTION, 'thrownpotion'], EntityLegacyIds::SPLASH_POTION);
        $factory->register(EnderPearl::class, function(World $world, CompoundTag $nbt) : EnderPearl{
            return new EnderPearl(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearl', EntityIds::ENDER_PEARL], EntityLegacyIds::ENDER_PEARL);
        $factory->register(CombatLogger::class, function(World $world, CompoundTag $nbt): CombatLogger{
            return new CombatLogger(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['CombatLogger', 'minecraft:combat_logger'], EntityLegacyIds::VILLAGER);
        $factory->register(BlackMarket::class, function(World $world, CompoundTag $nbt): BlackMarket{
            return new BlackMarket(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['BlackMarket', 'minecraft:black_market'], EntityLegacyIds::NPC);
        $factory->register(LegendaryMarket::class, function(World $world, CompoundTag $nbt): LegendaryMarket{
            return new LegendaryMarket(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['LegendaryMarket', 'minecraft:legendary_market'], EntityLegacyIds::NPC);
        $factory->register(TopOne::class, function(World $world, CompoundTag $nbt): TopOne{
            return new TopOne(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TopOne', 'minecraft:top_one'], EntityLegacyIds::NPC);
        $factory->register(TopTwo::class, function(World $world, CompoundTag $nbt): TopTwo{
            return new TopTwo(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TopTwo', 'minecraft:top_two'], EntityLegacyIds::NPC);
        $factory->register(TopThree::class, function(World $world, CompoundTag $nbt): TopThree{
            return new TopThree(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TopThree', 'minecraft:top_three'], EntityLegacyIds::NPC);
        $factory->register(BlockMarket::class, function(World $world, CompoundTag $nbt): BlockMarket{
            return new BlockMarket(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['BlockMarket', 'minecraft:block_market'], EntityLegacyIds::NPC);
        $factory->register(Switcher::class, function (World $world, CompoundTag $nbt) : Switcher {
            return new Switcher(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['Switcher', 'minecraft:switcher'], EntityLegacyIds::SNOWBALL);
        $factory->register(EggPorts::class, function (World $world, CompoundTag $nbt) : EggPorts {
            return new EggPorts(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['EggPorts', 'minecraft:eggports'], EntityLegacyIds::EGG);
       $factory->register(FireworksRocket::class, function(World $world, CompoundTag $nbt) : FireworksRocket{
            return new FireworksRocket(EntityDataHelper::parseLocation($nbt, $world), ItemFactory::getInstance()->get(ItemIds::FIREWORKS, 0));
        }, ['FireworksRocket', EntityIds::FIREWORKS_ROCKET], EntityLegacyIds::FIREWORKS_ROCKET);
       $factory->register(Enderman::class, function (World $world, CompoundTag $nbt) : Enderman {
           return new Enderman(EntityDataHelper::parseLocation($nbt, $world), $nbt);
       }, ['Enderman', EntityIds::ENDERMAN], EntityLegacyIds::ENDERMAN);
        $factory->register(Blaze::class, function (World $world, CompoundTag $nbt) : Blaze {
            return new Blaze(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Blaze', EntityIds::BLAZE], EntityLegacyIds::BLAZE);
        $factory->register(Cow::class, function (World $world, CompoundTag $nbt) : Cow {
            return new Cow(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Cow', EntityIds::COW], EntityLegacyIds::COW);
        $factory->register(Creeper::class, function (World $world, CompoundTag $nbt) : Creeper {
            return new Creeper(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Creeper', EntityIds::CREEPER]);
        $factory->register(Protector::class, function (World $world, CompoundTag $nbt) : Protector {
            return new Protector(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['Protector', EntityIds::SILVERFISH]);
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