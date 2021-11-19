<?php

namespace ImAMadDev\ability\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\ability\Ability;

class ResetPearl extends Ability {

	/** @var string */
	private string $name = 'Reset_Pearl';

	private string $description = "&eUsing it will remove the cooldown of the Ender pearl.\n&cHas a cooldown of 2 minutes";
	
	private int $cooldown = 180;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::GHAST_TEAR, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([TextFormat::colorize($this->description)]);
		return $item;
	}
	
	public function consume(HCFPlayer $player) : void {
		$time = (90 - (time() - PlayerData::getCountdown($player->getName(), $this->name)));
		if($time > 0) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $time));
			return;
		}
		if(!$player->getCooldown()->has('enderpearl')) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because don't have Ender Pearl cooldown");
			return;
		}
		PlayerData::setCountdown($player->getName(), $this->name, (time() + 80));
		$item = $player->getInventory()->getItemInHand();
		$player->getCooldown()->remove('enderpearl');
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
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
		return TextFormat::colorize("&dReset Pearl&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::GHAST_TEAR && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	#[Pure] public function isAbilityName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function obtain(HCFPlayer $player, int $count) : void {
		$status = $player->getInventoryStatus();
		if($status === "FULL") {
			$player->getWorld()->dropItem($player->getPosition()->asVector3(), $this->get($count), new Vector3(0, 0, 0));
        } else {
			$player->getInventory()->addItem($this->get($count));
        }
        $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GREEN . TextFormat::BOLD . $this->getColoredName());
    }

}