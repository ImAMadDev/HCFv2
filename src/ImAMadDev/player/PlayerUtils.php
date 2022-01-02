<?php

namespace ImAMadDev\player;

final class PlayerUtils {
	
	public const KILL_PRICE = 300;
	
	public const PUBLIC = 0;
	
	public const FACTION = 1;
	
	public const ALLY = 2;
	
	public const STAFF = 3;
	
	public const NONE = "None";
	
	public const ARCHER = "Archer";
	
	public const BARD = "Bard";
	
	public const MINER = "Miner";
	
	public const ROGUE = "Rogue";
	
	public const MAGE = "Mage";
	
	public static array $cooldownsNames = [
		'enderpearl' => "&dEnderPearl: ",
		'combattag' => "&cCombatTag: ",
		'strength_portable' => "&cStrength Portable: ",
		'anti_trapper' => "&2Anti Trapper: ",
		'storm_breaker' => "&aStorm Breaker: ",
		'home_teleport' => "&cHome: ",
		'reset_pearl' => "&5Reset Pearl: ",
		'effects_disabler' => "&2Effects Disabler: ",
		'effects_cooldown' => "&aEffects Cooldown: ",
		'archer_mark' => "&6Archer Mark: ",
		'stuck_teleport' => "&dStuck Teleport: ",
		'golden_apple' => "&eApple: ",
		're_pot' => "&bRe Pot: ",
		'backstab' => "&2BackStab: ",
		'freezing_portable' => "&bFreezing: ",
		'switcher' => "&eSwitcher: ",
		'pre_pearl' => "&dPre Pearl: ",
		'deleteblock' => "&cAnti Build Tag: ",
		'antitrapper_tag' => "&3Anti Trapper Tag: ",
		'logout' => "&9Logout: ",
	];

    public static array $Names = [
        'enderpearl' => "&dEnderPearl",
        'combattag' => "&cCombatTag",
        'strength_portable' => "&cStrength Portable",
        'anti_trapper' => "&2Anti Trapper",
        'storm_breaker' => "&aStorm Breaker",
        'home_teleport' => "&cHome",
        'reset_pearl' => "&5Reset Pearl",
        'effects_disabler' => "&2Effects Disabler",
        'effects_cooldown' => "&aEffects Cooldown",
        'archer_mark' => "&6Archer Mark",
        'stuck_teleport' => "&dStuck Teleport",
        'golden_apple' => "&eApple",
        're_pot' => "&bRe Pot",
        'backstab' => "&2BackStab",
        'freezing_portable' => "&bFreezing",
        'switcher' => "&eSwitcher",
        'pre_pearl' => "&dPre Pearl",
        'deleteblock' => "&cAnti Build Tag",
        'antitrapper_tag' => "&3Anti Trapper Tag",
        'logout' => "&9Logout",
    ];
}