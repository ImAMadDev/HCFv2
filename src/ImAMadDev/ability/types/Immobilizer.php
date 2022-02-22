<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\DamageOtherAbility;
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
use ImAMadDev\ability\ticks\ImmobilizerTick;

class Immobilizer extends DamageOtherAbility {

	/** @var string */
	private string $name = 'FreezingPortable';

	private string $description = "&eHit a player 3 times so that he/she cannot move.\n&cHas a cooldown of 3 minutes";
	
	public int $cooldown = 180;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::BLAZE_ROD, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::DAMAGE_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([TextFormat::colorize($this->description)]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player, HCFPlayer|Player $entity) : void {
		if($player->getCooldown()->has($this->name)) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $player->getCooldown()->get($this->name)));
			return;
		}
		$player->getCooldown()->add($this->name, $this->cooldown);
		HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ImmobilizerTick($entity), (5 * 20));
		$item = $player->getInventory()->getItemInHand();
		$item->setCount($item->getCount() - 1);
		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
		$entity->sendMessage(TextFormat::RED . "> " . TextFormat::YELLOW . "You have been hit with " . $this->getColoredName());
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
	}
	
	public function getHits() : int {
		return 2;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&r&bFreezing &rPortable&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::BLAZE_ROD && $item->getNamedTag()->getTag(self::DAMAGE_ABILITY) instanceof CompoundTag and $item->getCustomName() == $this->getColoredName()) {
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