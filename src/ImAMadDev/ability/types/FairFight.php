<?php

namespace ImAMadDev\ability\types;

use ImAMadDev\ability\Ability;
use ImAMadDev\ability\ticks\EffectDisablerTick;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class FairFight extends Ability
{

    /** @var string */
    private string $name = 'Fair_Fight';

    private string $description = "&eHit a player 3 times to remove all effects\n&cHas a cooldown of 2 minutes";

    private int $cooldown = 120;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
    public function get(int $count = 1, mixed $value = null): Item {
        $item = ItemFactory::getInstance()->get(BlockLegacyIds::IRON_BARS, 0, $count);
        $item->getNamedTag()->setString(self::ABILITY, CompoundTag::create());
        $item->getNamedTag()->setString(self::DAMAGE_ABILITY, CompoundTag::create());
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
        $item->setCustomName($this->getColoredName());
        $item->setLore([TextFormat::colorize($this->description)]);
        return $item;
    }

    public function consume(HCFPlayer $player, HCFPlayer $entity) : void {
        $time = (60 - (time() - $player->getCache()->getCountdown($this->getName())));
        if($time > 0) {
            $player->sendMessage(TextFormat::RED . "You can't use " . $this->getColoredName() . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $time));
            return;
        }
        $player->getCache()->setCountdown($this->getName(),  60);
        HCF::getInstance()->getScheduler()->scheduleRepeatingTask(new EffectDisablerTick($entity), 20);
        $item = $player->getInventory()->getItemInHand();
        $item->setCount($item->getCount() - 1);
        $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
        $entity->sendMessage(TextFormat::RED . "> " . TextFormat::YELLOW . "You have been hit with " . $this->getColoredName());
        $player->sendMessage(TextFormat::YELLOW . "You have consumed " . $this->getColoredName() . TextFormat::YELLOW . ", Now You have a countdown of " . TextFormat::BOLD . TextFormat::RED . gmdate('i:s', $this->cooldown));
    }

    public function getHits() : int {
        return 3;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getColoredName() : string {
        return TextFormat::colorize("&eFair Fight&r");
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isAbility(Item $item): bool {
        if($item->getId() === BlockLegacyIds::IRON_BARS && $item->getNamedTag()->getTag(self::DAMAGE_ABILITY) instanceof CompoundTag) {
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