<?php

namespace ImAMadDev\kit\classes;

use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\{
	Item,
	ItemIds,
	ItemFactory
};
use pocketmine\utils\{
	TextFormat,
	Limits
};
use pocketmine\player\Player;

class BardClass extends IEnergyClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Bard', [ItemIds::GOLD_HELMET, ItemIds::GOLDEN_CHESTPLATE, ItemIds::GOLDEN_LEGGINGS, ItemIds::GOLDEN_BOOTS]);
    }

    /**
     * @return array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 1, false),
                new EffectInstance(VanillaEffects::REGENERATION(), Limits::INT32_MAX, 1, false),
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
    
    public function isClickItem(Item $item): bool 
    {
    	return match ($item->getId()) {
            ItemIds::SUGAR, ItemIds::DYE, ItemIds::FEATHER, ItemIds::IRON_INGOT, ItemIds::SPIDER_EYE, ItemIds::BLAZE_POWDER, ItemIds::GHAST_TEAR, ItemIds::MAGMA_CREAM => true,
            default => false,
        };
    }
    
    public function isPassiveItem(Item $item): bool 
    {
    	return match ($item->getId()) {
            ItemIds::SUGAR, ItemIds::FEATHER, ItemIds::IRON_INGOT, ItemIds::BLAZE_POWDER, ItemIds::GHAST_TEAR, ItemIds::MAGMA_CREAM => true,
            default => false,
        };
    }
    
    public function getEnergyCost(Item $item): int | float 
    {
    	return match ($item->getId()) {
            ItemIds::SUGAR => 20,
            ItemIds::DYE, ItemIds::FEATHER, ItemIds::IRON_INGOT => 30,
            ItemIds::SPIDER_EYE, ItemIds::BLAZE_POWDER => 40,
            ItemIds::GHAST_TEAR => 35,
            ItemIds::MAGMA_CREAM => 25,
            default => 0,
        };
    }
    
    public function getEffectPassiveItem(Item $item): ?EffectInstance 
    {
    	switch($item->getId()) {
    		case ItemIds::SUGAR:
    			return new EffectInstance(VanillaEffects::SPEED(), 20 * 5, 1, true);
			case ItemIds::FEATHER:
				return new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 5, 2, true);
			case ItemIds::IRON_INGOT:
				return new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 5, 0, true);
			case ItemIds::GHAST_TEAR:
				return new EffectInstance(VanillaEffects::REGENERATION(), 20 * 5, 0, true);
			case ItemIds::BLAZE_POWDER:
				return new EffectInstance(VanillaEffects::STRENGTH(), 20 * 5, 0, true);
		}
		return null;
    }
    
    public function itemConsumed(HCFPlayer $player, Item $item): bool
    {
    	if(parent::itemConsumed($player, $item)) {
      	  if ($item->getId() == ItemIds::SUGAR) {
        		$effect = new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2);
        		$this->applyNearbyAllies($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
        		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
        		$player->getCooldown()->add('effects_cooldown', 10);
        		$player->getCooldown()->add('combattag', 30);
        	    $player->sendMessage(TextFormat::colorize("&eYou have consumed &bSpeed 3"));
        		return true;
        	}
       	 if ($item->getId() == ItemIds::FEATHER) {
        		$effect = new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 15, 6);
        		$this->applyNearbyAllies($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
         	   $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &aJump Boost 7"));
          	  return true;
            }
            if ($item->getId() == ItemIds::GHAST_TEAR) {
        		$effect = new EffectInstance(VanillaEffects::REGENERATION(), 20 * 10, 2);
        		$this->applyNearbyAllies($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &cRegeneration 3"));
          	  return true;
            }
            if($item->getId() == ItemIds::DYE and $item->getMeta() == 0) {
            	$effect = new EffectInstance(VanillaEffects::INVISIBILITY(), 30 * 10, 0);
            	$this->applyNearbyAllies($player, $effect);
            	$player->applyPotionEffect($effect);
            	$player->getClassEnergy()->reduce($this->getEnergyCost($item));
                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                $player->getCooldown()->add('effects_cooldown', 10);
                $player->getCooldown()->add('combattag', 30);
                $player->sendMessage(TextFormat::colorize("&eYou have consumed &fInvisibility 1"));
            }
            if ($item->getId() == ItemIds::BLAZE_POWDER) {
        		$effect = new EffectInstance(VanillaEffects::STRENGTH(), 20 * 10, 1);
        		$this->applyNearbyAllies($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &6Strength 2"));
          	  return true;
            }
            if ($item->getId() == ItemIds::IRON_INGOT) {
        		$effect = new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 10, 3);
        		$this->applyNearbyAllies($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &7Resistance 3"));
          	  return true;
            }
            if ($item->getId() == ItemIds::SPIDER_EYE) {
        		$effect = new EffectInstance(VanillaEffects::WITHER(), 20 * 10, 1);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &9Wither 2"));
          	  return true;
            }
            if ($item->getId() == ItemIds::MAGMA_CREAM) {
        		$effect = new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 50 * 50, 1);
        		$this->applyNearbyEnemys($player, $effect);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
            	$player->getCooldown()->add('effects_cooldown', 10 );
           	 $player->getCooldown()->add('combattag', 30);
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &4Fire Resistance 2"));
          	  return true;
            }
        }
        return false;
    }
}