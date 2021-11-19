<?php

namespace ImAMadDev\kit;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;
use ImAMadDev\player\HCFPlayer;

use function count;

abstract class Kit {

	/** @var string */
	private string $name;

	/** @var Item */
	private Item $icon;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission;
	
	/** @var string */
	private string $description = "";

	/**
	 * @return string
	 */
	abstract public function getPermission(): string;

	/**
	 * @return array
	 */
	abstract public function getArmor(): array;

	/**
	 * @return Item
	 */
	abstract public function getIcon(): Item;

	/**
	 * @return array
	 */
	abstract public function getItems(): array;

	/**
	 * @return string
	 */
	abstract public function getName(): string;

	/**
	 * @param string $name
	 * @return bool
	 */
	abstract public function isKit(string $name): bool;
	
	abstract public function getDescription(): string;


	abstract public function giveKit(HCFPlayer $player);

}