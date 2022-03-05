<?php

namespace ImAMadDev\kit\classes;

use ImAMadDev\player\HCFPlayer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\StringToEffectParser;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\utils\{
	TextFormat,
	Limits
};
use pocketmine\player\Player;
use ImAMadDev\manager\ClaimManager;

class CustomEnergyClass extends IEnergyClass
{
	
	private int | float $energy;
	
	private array $effects;
	
	private array $items = [];
	
	private array $energy_items = [];
	private array $click_effect_items = [];
	private array $passive_effect_items = [];
	
    public function __construct(string $className, array $armor, array $items, int | float $energy, array $effects = [])
    {
        parent::__construct($className, $armor);
        $this->energy = $energy;
        $this->effects = $effects;
        $this->parseItems($items);
    }

    /**
     * @return EffectInstance[] array
     */
    public function getEffects() : array
    {
        return $this->effects;
    }
    
    public function getClickItems(): array 
    {
    	return $this->click_effect_items;
    }
    
    public function getPassiveItems(): array 
    {
    	return $this->passive_effect_items;
    }
    
    public function isClickItem(Item $item): bool 
    {
    	if(!isset($this->click_effect_items[$item->getId().':'.$item->getMeta()])) return false;
    	return true;
    }
    
    public function isPassiveItem(Item $item): bool 
    {
    	if(!isset($this->passive_effect_items[$item->getId().':'.$item->getMeta()])) return false;
    	return true;
    }
    
    public function getEnergyCost(Item $item): int | float
    {
    	if(!isset($this->energy_items[$item->getId().':'.$item->getMeta()])) return 0;
    	return $this->energy_items[$item->getId().':'.$item->getMeta()];
    }
    
    public function getEffectClickItem(Item $item): ?EffectInstance 
    {
    	if(!isset($this->click_effect_items[$item->getId().':'.$item->getMeta()])) return null;
    	return $this->click_effect_items[$item->getId().':'.$item->getMeta()];
    }
    
    public function getEffectPassiveItem(Item $item): ?EffectInstance  
    {
    	if(!isset($this->passive_effect_items[$item->getId().':'.$item->getMeta()])) return null;
    	return $this->passive_effect_items[$item->getId().':'.$item->getMeta()];
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
        return $this->energy;
    }
    
    private function parseItems(array $items)
    {
    	foreach($items as $i) {
    		$this->items[$i['Id'].':'.$i['meta']] = ItemFactory::getInstance()->get($i['Id'], $i['meta'], 1);
    		$this->energy_items[$i['Id'].':'.$i['meta']] = $i['energy_price'];
  		  $this->click_effect_items[$i['Id'].':'.$i['meta']] = new EffectInstance(StringToEffectParser::getInstance()->parse($i['click_effect']['name']), (intval($i['click_effect']['duration']) * 20), intval($i['click_effect']['amplifier']), true);
  		  if(isset($i['passive_effect']['name'])) 
  	  		$this->passive_effect_items[$i['Id'].':'.$i['meta']] = new EffectInstance(StringToEffectParser::getInstance()->parse($i['passive_effect']['name']), (5 * 20), intval($i['passive_effect']['amplifier']), true);
    	}
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    public function itemConsumed(HCFPlayer $player, Item $item): bool
    {
    	if(parent::itemConsumed($player, $item)) {
           $effect = $this->getEffectClickItem($item);
           if($effect instanceof EffectInstance) {
              $player->applyPotionEffect($effect);
              $player->getClassEnergy()->reduce($this->getEnergyCost($item));
              $item->setCount($item->getCount() - 1);
              $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
              $player->getCooldown()->add('effects_cooldown', 10);
              return true;
           }
    	}
       return false;
    }
}