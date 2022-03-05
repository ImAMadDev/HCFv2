<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\utils\InteractionAbility;
use ImAMadDev\rank\RankClass;
use JetBrains\PhpStorm\Pure;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\item\Fireworks;
use ImAMadDev\entity\projectile\FireworksRocket;
use pocketmine\world\Position;

class RankSharp extends InteractionAbility {

	/**
     * @var string $name
     */
	private string $name = 'RankSharp';

	private string $description;
	
    public int $cooldown = 0;

	public function __construct() {
		$this->description = "§r§fTap this §r§l§6Rank Sharp§r §fon the ground to redeem partner rewards. \n§r§7rewards (5-7)\n§r§eAvailable at §r§6pacmanlife.buycraft.net";
	}

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	public function get(int $count = 1, mixed $value = null): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, $count);
        $item->getNamedTag()->setTag(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setTag(self::INTERACT_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
        $item->getNamedTag()->setString("rank", $value['rank']);
        $item->getNamedTag()->setString("duration", $value['duration']);
        $timeFormated = (time() + HCFUtils::strToSeconds($value['duration']));
        $this->description = "§fTouch this §r§l§6Rank Sharp§r §fanywhere to claim rank: §6{$value['rank']}§f, duration: §6" . HCFUtils::getTimeString($timeFormated);
		$item->setCustomName($this->getColoredName());
		$item->setLore([$this->description]);
		return $item;
	}
	
	public function consume(HCFPlayer|Player $player) : void {
		$item = $player->getInventory()->getItemInHand();
        $rank = $item->getNamedTag()->getString("rank", "");
        $duration = $item->getNamedTag()->getString("duration", "permanent");
        if ($rank !== ""){
            if ($player->hasRank($rank)){
                $player->sendMessage(TextFormat::RED . "You already have this rank.");
                return;
            } else {
                if (($rankClass = HCF::$rankManager->getRank($rank)) instanceof RankClass) {
                    HCF::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "rank give " . $player->getName() . " " . $rank . " " . $duration);
                    $player->sendMessage(TextFormat::DARK_GREEN . "You have reclaimed the rank sharp: " . TextFormat::colorize($rankClass->getTag()));
                } else {
                    $player->sendMessage(TextFormat::RED . "An unknown error occurred!");
                    return;
                }
            }
        } else {
            $player->sendMessage(TextFormat::RED . "Contact the developer.");
            return;
        }
        $this->spawnFirework($player->getPosition());
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
		return TextFormat::colorize("&r&l&6Rank Sharp&r");
	}

    /**
     * @param Item $item
     * @return bool
     */
	public function isAbility(Item $item): bool {
		if($item->getId() === ItemIds::EMERALD && $item->getNamedTag()->getTag(self::INTERACT_ABILITY) instanceof CompoundTag and $item->getCustomName() == $this->getColoredName()) {
			return true;
		}
		return false;
	}
	
	#[Pure] public function isAbilityName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
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