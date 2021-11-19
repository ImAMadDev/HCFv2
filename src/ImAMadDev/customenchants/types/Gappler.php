<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\customenchants\CustomEnchantmentIds;
use ImAMadDev\utils\HCFUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Event;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Gappler extends CustomEnchantment
{

    /**
     * GapplerEnchantment Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::NONE, 4);
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return CustomEnchantmentIds::GAPPLER;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return CustomEnchantment::GAPPLER;
    }

    /**
     * @param int $level
     * @return EffectInstance
     */
    public function getEffectsByEnchantment(int $level): EffectInstance
    {
        return new EffectInstance(VanillaEffects::CONDUIT_POWER(), 60, ($level - 1));
    }

    /**
     * @return int
     */
    public function getEnchantmentPrice(): int
    {
        return 50000;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1): string
    {
        return TextFormat::RESET . TextFormat::DARK_RED . $this->getName() ." " . HCFUtils::getGreekFormat($level);
    }

    /**
     * @return bool
     */
    public function canBeActivate() : bool
    {
        return true;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function canEnchant(Item $item): bool {
        return in_array($item->getId(), CustomEnchantment::GAPPLER_ITEMS);
    }

    /**
     * @param Item $item
     * @param int|null $slot
     * @param Player|null $player
     * @param int $level
     * @param Event|null $event
     */
    public function activate(Item $item, ?int $slot = null, ?Player $player = null, int $level = 1, ?Event $event = null): void {
    }

    public function calculateTime(int $level) : int
    {
        return 30 - $level * 5;
    }
}