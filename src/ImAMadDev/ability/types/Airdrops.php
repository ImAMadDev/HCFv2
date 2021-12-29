<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\InteractionBlockAbility;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\ItemFactory;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ability\ticks\AirDropTick;
use ImAMadDev\manager\ClaimManager;

class Airdrops extends InteractionBlockAbility {

	/** @var string */
	private string $name = 'Airdrops';

	private string $description;

	public function __construct() {
		$this->description = TextFormat::colorize("&dRight Click to open");
	}

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(BlockLegacyIds::OBSERVER, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::PLACE_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([$this->description]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player, Block $block, int $face = Facing::UP) : void {
		if($block->getId() === BlockLegacyIds::AIR) {
			return;
		}
		$claim = ClaimManager::getInstance()->getClaimByPosition($block->getPosition());
		if($claim !== null) {
			if(!$claim->canEdit($player->getFaction())) {
				return;
			}
		}
		HCF::getInstance()->getScheduler()->scheduleRepeatingTask(new AirDropTick($block->getPosition(), $face), 20);
		$item = $player->getInventory()->getItemInHand();
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName());
		$item->setCount($item->getCount() - 1);
		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&5&k!!&r&l&3Air Drops&5&k!!&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === BlockLegacyIds::OBSERVER && $item->getNamedTag()->getTag(self::PLACE_ABILITY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	#[Pure] public function isAbilityName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function obtain(HCFPlayer|Player $player, int $count) : void {
		$status = $player->getInventoryStatus();
		if($status === "FULL") {
			$player->getWorld()->dropItem($player->getPosition()->asVector3(), $this->get($count), new Vector3(0, 0, 0));
        } else {
			$player->getInventory()->addItem($this->get($count));
        }
        $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GREEN . TextFormat::BOLD . $this->getColoredName());
    }

}