<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Armor;
abstract class IClass
{

    public function __construct(
        public string $name,
        public array $armor
    ){}
    
    public function getName(): string 
    {
    	return $this->name;
    }

    abstract public function getEffects() : array;

    public function isThis(ArmorInventory $armor): bool
    {
        $helmet = $armor->getHelmet()->getId();
        $chestplate = $armor->getChestplate()->getId();
        $leggings = $armor->getLeggings()->getId();
        $boots = $armor->getBoots()->getId();
        if($this->armor[0] instanceof Armor) {
        	return ($helmet == $this->armor[0]->getId() and $chestplate == $this->armor[1]->getId() and $leggings == $this->armor[2]->getId() and $boots == $this->armor[3]->getId());
        }
        return ($helmet == $this->armor[0] and $chestplate == $this->armor[1] and $leggings == $this->armor[2] and $boots == $this->armor[3]);
    }
    
    /**
	 * @param string $name
	 * @return bool
	 */
	#[Pure] public function isClass(string $name): bool {
		return strtolower($this->getName()) == strtolower($name);
	}
}