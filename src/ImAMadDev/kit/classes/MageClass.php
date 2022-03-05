<?php

namespace ImAMadDev\kit\classes;

use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\Player;
use pocketmine\item\{
	ItemIds,
	ItemFactory,
	Item
};
use pocketmine\utils\{
	TextFormat,
	Limits
};

class MageClass extends IEnergyClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Mage', [ItemIds::GOLD_HELMET, ItemIds::CHAIN_CHESTPLATE, ItemIds::CHAIN_LEGGINGS, ItemIds::GOLDEN_BOOTS]);
    }

    /**
     * @return array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 1, false),
                new EffectInstance(VanillaEffects::REGENERATION(), Limits::INT32_MAX, 0, false),
                new EffectInstance(VanillaEffects::RESISTANCE(), Limits::INT32_MAX, 1, false)
            ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float|int
     */
    public function getMaxEnergy(): float|int
    {
        return 120.0;
    }
    
    public function isPassiveItem(Item $item): bool 
    {
    	return false;
    }
    
    public function getEffectPassiveItem(Item $item): ?EffectInstance 
    {
    	return null;
    }
    
    public function isClickItem(Item $item): bool 
    {
    	return match ($item->getId()) {
            ItemIds::DYE, ItemIds::ROTTEN_FLESH, ItemIds::GOLDEN_NUGGET, ItemIds::COAL, ItemIds::SPIDER_EYE => true,
            default => false,
        };
    }
    
    public function getEnergyCost(Item $item): int | float 
    {
    	return match ($item->getId()) {
            ItemIds::DYE => 45,
            ItemIds::ROTTEN_FLESH => 25,
            ItemIds::GOLDEN_NUGGET => 35,
            ItemIds::COAL, ItemIds::SPIDER_EYE => 30,
            ItemIds::SEEDS => 40,
            default => 0,
        };
    }
    
    public function itemConsumed(HCFPlayer $player, Item $item): bool
    {
    	if(parent::itemConsumed($player, $item)) {
      	  if ($item->getId() == ItemIds::COAL) {
        		$effect = new EffectInstance(VanillaEffects::WEAKNESS(), 20 * 15, 0);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
        		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
        		$player->getCooldown()->add('effects_cooldown', 10);
        		$player->getCooldown()->add('combattag', 30);
        	    $player->sendMessage(TextFormat::colorize("&eYou have consumed &7Waekness 1"));
        		return true;
        	}
       	 if ($item->getId() == ItemIds::ROTTEN_FLESH) {
        		$effect = new EffectInstance(VanillaEffects::HUNGER(), 20 * 15, 0);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
         	   $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &aHunger 1"));
          	  return true;
            }
            if ($item->getId() == ItemIds::GOLDEN_NUGGET) {
        		$effect = new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 15, 1);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &7Slowness 2"));
          	  return true;
            }
            if($item->getId() == ItemIds::DYE and $item->getMeta() == 2) {
            	$effect = new EffectInstance(VanillaEffects::POISON(), 20 * 15, 0);
            	$this->applyNearbyEnemys($player, $effect);
            	$player->getClassEnergy()->reduce($this->getEnergyCost($item));
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                $player->getCooldown()->add('effects_cooldown', 10);
                $player->getCooldown()->add('combattag', 30);
                $player->sendMessage(TextFormat::colorize("&eYou have consumed &2Poison 1"));
            }
            if ($item->getId() == ItemIds::SEEDS) {
        		$effect = new EffectInstance(VanillaEffects::NAUSEA(), 20 * 15, 0);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &2Nausea 1"));
          	  return true;
            }
            if ($item->getId() == ItemIds::SPIDER_EYE) {
        		$effect = new EffectInstance(VanillaEffects::WITHER(), 20 * 10, 1);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &9Wither 2"));
          	  return true;
            }
        }
        return false;
    }
}