<?php

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\world\{World, Position};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\koth\KOTHArena;
use ImAMadDev\manager\EOTWManager;
use ImAMadDev\faction\Faction;

class EditableClaim extends Claim {
	
	public function __construct(HCF $main, array $data) {
		parent::__construct($main, $data);
	}
}