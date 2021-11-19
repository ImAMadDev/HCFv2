<?php

namespace ImAMadDev\ability\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\entity\effect\EffectInstance;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ability\Ability;

class ResistancePortable extends Ability {

	/** @var string */
	private string $name = 'Resistance_Portable';

	private string $description;
	
	private int $cooldown = 20;
	
	public function __construct() {
		$this->description = TextFormat::colorize("&dRight Click to activate\n&7&oThis item gives you 5 seconds of &6[Resistance III]&r");
	}

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::IRON_INGOT, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([$this->description]);
		return $item;
	}
	
	public function consume(HCFPlayer $player) : void {
		if($player->getCooldown()->has('effects_cooldown')) {
			$player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
			return;
		}
		$player->getCooldown()->add('effects_cooldown', $this->cooldown);
		$effect = new EffectInstance(VanillaEffects::RESISTANCE(), (20 * 6), 2, true);
		foreach($player->getNearbyPlayers(40, 40) as $nearby) {
			$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
			if($player->getFaction() !== null) {
				if($player->getFaction()->isInFaction($nearby->getName()) or $player->getFaction()->isAlly($nearbyFaction)) {
					$nearby->applyPotionEffect($effect);
				}
			}
		}
		$player->applyPotionEffect($effect);
		$item = $player->getInventory()->getItemInHand();
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
		return TextFormat::colorize("&7Resistance Portable&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::IRON_INGOT && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag) {
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
        $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GRAY . TextFormat::BOLD . $this->getColoredName());
    }

}