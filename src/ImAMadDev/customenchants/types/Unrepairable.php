<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\customenchants\CustomEnchantmentIds;
use ImAMadDev\utils\HCFUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class Unrepairable extends CustomEnchantment
{

    /**
     * UnrepairableEnchantment Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::ALL, ItemFlags::NONE, 1);
    }

    /**
     * @return int
     */
    public function getId() : int {
        return CustomEnchantmentIds::UNREPAIRABLE;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::UNREPAIRABLE;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
        return TextFormat::RESET . TextFormat::DARK_RED . $this->getName() . " " . HCFUtils::getGreekFormat($level);
    }

    /**
     * @return EffectInstance
     */
    public function getEffectsByEnchantment(int $level = 1) : EffectInstance {
        return new EffectInstance(VanillaEffects::CONDUIT_POWER(), 60, ($level - 1));
    }

    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
        return 25000;
    }

    /**
     * @return bool
     */
    public function canBeActivate(): bool {
        return false;
    }

    public function canEnchant(Item $item): bool {
        return true;
    }
}