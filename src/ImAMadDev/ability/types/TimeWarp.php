<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\InteractionAbility;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\entity\effect\EffectInstance;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ability\Ability;
use ImAMadDev\ability\ticks\TimeWarpTick;

class TimeWarp extends InteractionAbility {

	/** @var string */
	private string $name = 'TimeWarp';

	private string $description = "&eWhen you use it you will be teleported to the last position where you use the last EnderPearl.\n&cHas a cooldown of 1 minute";
	
	public int $cooldown = 60;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::FEATHER, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([TextFormat::colorize($this->description)]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player) : void {
		$time = (30 - (time() - $player->getCache()->getCountdown($this->getName())));
		if($time > 0) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $time));
			return;
		}
		if($player->getEnderpearlHistory()->has() === false) {
			$player->sendMessage(TextFormat::RED . "You have not used an Enderpearl in the last 10 seconds.");
			return;
		}
		$player->getCache()->setCountdown($this->getName(), 30);
		HCF::getInstance()->getScheduler()->scheduleDelayedTask(new TimeWarpTick($player), (5 * 20));
		$item = $player->getInventory()->getItemInHand();
		$item->setCount($item->getCount() - 1);
		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&gTimeWarp&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::FEATHER && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag and $item->getCustomName() == $this->getColoredName()) {
			return true;
		}
		return false;
	}
	
	#[Pure] public function isAbilityName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function obtain(HCFPlayer|Player $player, int $count) : void {
		$status = $player->getInventoryStatus(1);
		if($status === "FULL") {
			$player->getWorld()->dropItem($player->getPosition()->asVector3(), $this->get($count), new Vector3(0, 0, 0));
        } else {
			$player->getInventory()->addItem($this->get($count));
        }
        $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GREEN . TextFormat::BOLD . $this->getColoredName());
    }
}