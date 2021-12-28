<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\InteractionAbility;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\entity\Location;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\item\Fireworks;
use ImAMadDev\entity\projectile\FireworksRocket;
use ImAMadDev\manager\AbilityManager;
use pocketmine\world\Position;

class SummerLootbox extends InteractionAbility {

	/**
     * @var string $name
     */
	private string $name = 'SummerLootbox';

	private string $description;

	public function __construct() {
		$this->description = "§r§fTap this §r§l§dSummer Lootbox §r §fon the ground to redeem partner rewards. \n§r§7rewards (5-7)\n§r§eAvailable at §r§6pacmanlife.buycraft.net";
	}

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(BlockLegacyIds::MOB_HEAD_BLOCK, 4, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$item->setCustomName($this->getColoredName());
		$item->setLore([$this->description]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player) : void {
		$item = $player->getInventory()->getItemInHand();
		$player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName());
		$item->setCount($item->getCount() - 1);
		$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
		$this->spawnFirework($player->getPosition());
		foreach($this->getContents(rand(5, 7)) as $price) {
			if($player->getInventory()->canAddItem($price)) {
				$player->getInventory()->addItem($price);
			} else {
				$player->getWorld()->dropItem($player->getPosition()->asVector3(), $price, new Vector3(0, 0, 0));
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&r&l&dSummer Lootbox &r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === BlockLegacyIds::MOB_HEAD_BLOCK && $item->getMeta() == 4 && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	#[Pure] public function isAbilityName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function getContents(int $count = 5): array {
		$items = [];
		$list = [];
		foreach(AbilityManager::getInstance()->getAbilities() as $ability) {
			if($ability->getName() === "SummerLootbox" and $ability->getName() === "RankSharp") {
				continue;
			}
			if($ability->getName() === "Airdrops") {
				$list[] = $ability->get();
			}elseif($ability->getName() === "PartnerPackages") {
                $list[] = $ability->get(rand(1, 2));
            }else {
				$list[] = $ability->get(rand(2, 3));
			}
		}
		shuffle($list);
		for($i = 0; $i < $count; $i++){
			$items[] = $list[array_rand($list)];
		}
		return $items;
	}
	
	public function spawnFirework(Position $pos) {
        $data = new Fireworks(new ItemIdentifier(ItemIds::FIREWORKS, 0));
        $data->addExplosion($data->getRandomType(), $data->getRandomColor(), "", 1, 1);
        $entity = new FireworksRocket(Location::fromObject($pos->add(0.5, 0, 0.5), $pos->getWorld(), lcg_value() * 360, 90), $data);
        $entity->spawnToAll();
        $entity->setLifeTime(1);
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