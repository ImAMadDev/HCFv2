<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\InteractionAbility;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ability\ticks\PrePearlTick;

class PrePearl extends InteractionAbility {

	/** @var string */
	private string $name = 'PrePearl';

	private string $description = "&eWhen you use it you will have a cooldown of 15 seconds,\n&eat the end of this cooldown you will be\n&ereturned to the position where you used this item.\n&cHas a cooldown of 3 minutes";
	
	public int $cooldown = 180;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([TextFormat::colorize($this->description)]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player) : void {
		if($player->getCooldown()->has($this->name)) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $player->getCooldown()->get($this->name)));
			return;
		}
		HCF::getInstance()->getScheduler()->scheduleDelayedTask(new PrePearlTick($player), (15 * 20));
		$player->getCooldown()->add($this->name, $this->cooldown);
		$item = $player->getInventory()->getItemInHand();
		$item->setCount($item->getCount() - 1);
		$player->sendMessage(TextFormat::RED . "> " . TextFormat::YELLOW . "In 15 seconds you will be returned to the position where you used this ability!");
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
		return TextFormat::colorize("&dPre Pearl&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::SPAWN_EGG && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag and $item->getCustomName() == $this->getColoredName()) {
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