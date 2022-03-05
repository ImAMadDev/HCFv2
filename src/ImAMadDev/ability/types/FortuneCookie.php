<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\InteractionAbility;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\entity\effect\EffectInstance;

use ImAMadDev\player\HCFPlayer;

class FortuneCookie extends InteractionAbility {

	/** @var string */
	private string $name = 'FortuneCookie';

	private string $description;
	
	public int $cooldown = 120;
	
	public function __construct() {
		$this->description = TextFormat::colorize("&7Eat this item to have a chance\n&7to receive Nausea or Strength!");
	}

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::COOKIE, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([$this->description]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player) : void {
		if($player->getCooldown()->has($this->name)) {
			$player->sendTip(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $player->getCooldown()->get($this->name)));
			return;
		}
		$player->getCooldown()->add($this->name, $this->cooldown);
		if (rand(0, 100) > 50) {
			$player->sendMessage(TextFormat::colorize("&7&m---------------------------\n &6» &fYou have ate a &6&lFortune Cookie&r\n &6» &fand received &eStrength 2&f!\n&c &r\n &6» &fDuration: &e7 Seconds\n&7&m---------------------------"));
			$player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), (20 * 7), 1, true));
		} else {
			$player->sendMessage(TextFormat::colorize("&7&m---------------------------\n &6» &fYou have ate a &6&lFortune Cookie&r\n &6» &fand received &eNausea 2&f!\n&c &r\n &6» &fDuration: &e7 Seconds\n&7&m---------------------------"));
			$player->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), (20 * 7), 1, true));
		}
		$item = $player->getInventory()->getItemInHand();
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
		$item->setCount($item->getCount() - 1);
		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&6Fortune Cookie&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::COOKIE && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag and $item->getCustomName() == $this->getColoredName()) {
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
        $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GOLD . TextFormat::BOLD . $this->getColoredName());
    }

}