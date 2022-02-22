<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\DamageOtherAbility;
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

use ImAMadDev\player\HCFPlayer;

class PortableRogue extends DamageOtherAbility {

	/** @var string */
	private string $name = 'PortableRogue';

	private string $description = "&ehits a player to remove 6 hearts.\n&cHas a cooldown of 5 minutes ";
	
	public int $cooldown = 300;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::GOLDEN_SWORD, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::DAMAGE_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([TextFormat::colorize($this->description)]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player, HCFPlayer|Player $entity) : void {
		$time = (150 - (time() - $player->getCache()->getCountdown($this->getName())));
		if($time > 0) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $time));
			return;
		}
        $player->getCache()->setCountdown($this->getName(), 150);
		$entity->setHealth($entity->getHealth() / 2);
		$player->getInventory()->setItemInHand(ItemFactory::air());
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5 * 20, 3));
		$player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5 * 20, 3));
		$entity->sendTitle(TextFormat::RED . TextFormat::BOLD . "Backstabbed!");
		$entity->sendMessage(TextFormat::RED . "> " . TextFormat::YELLOW . "You have been hit with " . $this->getColoredName());
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&ePortable Rogue&r");
	}
	
	public function getHits() : int {
		return 1;
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::GOLDEN_SWORD && $item->getNamedTag()->getTag(self::DAMAGE_ABILITY) instanceof CompoundTag and $item->getCustomName() == $this->getColoredName()) {
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