<?php

namespace ImAMadDev\ability;

use pocketmine\item\Item;

abstract class Ability {

	/** @var string */
	private string $name;
	
	public const ABILITY = "Ability";
	
	public const INTERACT_ABILITY = "Interaction";
	
	public const DAMAGE_ABILITY = "Damageable";

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	abstract public function get(int $count = 1, mixed $value = null): Item;

	/**
	 * @return string
	 */
	abstract public function getName(): string;
	
	
	abstract public function getColoredName(): string;
	
	
	abstract public function isAbility(Item $item): bool;

}