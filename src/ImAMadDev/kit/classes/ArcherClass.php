<?php

namespace ImAMadDev\kit\classes;

use ImAMadDev\player\HCFPlayer;
use pocketmine\player\Player;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\{Item, ItemIds, ItemFactory, VanillaItems};
use pocketmine\utils\{
	TextFormat,
	Limits
};

class ArcherClass extends IEnergyClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Archer', [ItemIds::LEATHER_CAP, ItemIds::LEATHER_CHESTPLATE, ItemIds::LEATHER_LEGGINGS, ItemIds::LEATHER_BOOTS]);
    }

    /**
     * @return EffectInstance[] array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 2, false),
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
        return 60.0;
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
            ItemIds::SUGAR => true,
            ItemIds::FEATHER => true,
            default => false,
        };
    }
    
    public function getEnergyCost(Item $item): int | float 
    {
    	return match ($item->getId()) {
            ItemIds::SUGAR => 20,
            ItemIds::FEATHER => 30,
            default => 0,
        };
    }
    
    public function itemConsumed(HCFPlayer $player, Item $item): bool
    {
    	if(parent::itemConsumed($player, $item)) {
      	  if ($item->getId() == ItemIds::SUGAR) {
        		$effect = new EffectInstance(VanillaEffects::SPEED(), 20 * 10, 3);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
            	$player->getCooldown()->add('effects_cooldown', 10 );
        	    $player->sendMessage(TextFormat::colorize("&eYou have consumed &bSpeed"));
        		return true;
        	}
       	 if ($item->getId() == ItemIds::FEATHER) {
        		$effect = new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 10, 3);
        		$player->applyPotionEffect($effect);
        		$player->getClassEnergy()->reduce($this->getEnergyCost($item));
        		$item->setCount($item->getCount() - 1);
            	$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
            	$player->getCooldown()->add('effects_cooldown', 10 );
          	  $player->sendMessage(TextFormat::colorize("&eYou have consumed &aJump Boost"));
          		return true;
            }
        }
        return false;
    }
}