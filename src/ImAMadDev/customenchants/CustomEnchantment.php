<?php

namespace ImAMadDev\customenchants;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\entity\effect\EffectInstance;

abstract class CustomEnchantment extends Enchantment {
	
	public const SPEED = 'Speed';
	public const BURN_SHIELD = 'Burn Shield';
	public const INVISIBILITY = 'Invisibility';
	public const JUMP_BOOST = 'Jump Boost';
	public const GLOW = 'Glow';
	public const STRENGTH = 'Strength';
	public const HELL_FORGET = 'Hell Forget';
	public const KEY_FINDER = 'Key Finder';
	public const NUTRITION = 'Nutrition';
	public const IMPLANTS = 'Implants';
	public const FORTUNE = 'Fortune';
	public const SMELTING = 'Smelting';
	public const LIFE_STEAL = 'Life Steal';
	public const OVERLOAD = 'Overload';
	public const GAPPLER = 'Gappler';
	public const UNREPAIRABLE = 'Unrepairable';
    public const MERMAID = 'Mermaid';
	
	public const HELL_FORGET_ITEMS = [298, 299, 300, 301, 302, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317];
	public const IMPLANTS_ITEMS = [312];
	public const OVERLOAD_ITEMS = [312];
	public const KEY_FINDER_ITEMS = [278];
	public const FORTUNE_ITEMS = [257, 285, 278, 274];
	public const SPEED_ITEMS = [301, 305, 309, 313, 317];
	public const BURN_SHIELD_ITEMS = [299, 303, 307, 311, 315];
    public const GAPPLER_ITEMS = [311];
	public const INVISIBILITY_ITEMS = [298, 302, 306, 310, 314];
	public const JUMP_BOOST_ITEMS = [301, 305, 309, 313, 317];
	public const STRENGTH_ITEMS = [310, 469];
	public const GLOW_ITEMS = [298, 302, 306, 310, 314];
	public const NUTRITION_ITEMS = [276];
	public const LIFE_STEAL_ITEMS = [276];
	public const SMELTING_ITEMS = [257, 278];
    public const MERMAID_ITEMS = [310];

    #[Pure] public function __construct(string $name, int $rarity, int $primaryItemFlags, int $secondaryItemFlags, int $maxLevel = 1){
		parent::__construct($name, $rarity, $primaryItemFlags, $secondaryItemFlags, $maxLevel);
	}
	
	abstract public function getEffectsByEnchantment(int $level) : EffectInstance;
	
	abstract public function getEnchantmentPrice() : int;
	
	abstract public function getNameWithFormat(int $level = 1) : string;

}