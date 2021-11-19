<?php

namespace ImAMadDev\crate;

use pocketmine\block\Block;
use ImAMadDev\player\HCFPlayer;

abstract class Crate {

	/** @var string */
	private string $name;
	
	public const KEY_TAG = "CrateKey";

	
	public const BLOCK_CRATES = [247, 116, 138, 130, 120, 25, 91, 58, 86, 47];

	/**
	 * @return array
	 */
	abstract public function getInventory(): array;

	/**
	 * @return string
	 */
	abstract public function getName(): string;
	
	
	abstract public function getColoredName(): string;
	
	
	abstract public function isCrate(Block $block): bool;


	abstract public function getContents(HCFPlayer $player);

}