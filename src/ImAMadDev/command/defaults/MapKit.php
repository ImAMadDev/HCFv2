<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\faction\FactionUtils;
use ImAMadDev\utils\HCFUtils;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MapKit extends Command
{

    public function __construct()
    {
        parent::__construct("mapkit", "see the configuration of this map", "/mapkit", ["mk"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $sender->sendMessage(TextFormat::colorize($this->getMapKit()));
    }

    private function getMapKit() : string
    {
        return '&7&m--------------------------------' . TextFormat::EOL .
            '&6&lMapkit&7: &r' . TextFormat::EOL .
            '&eProtection &f' . HCFUtils::PAID_PROTECTION . '&7, &eSharpness &f' . HCFUtils::PAID_SHARPNESS . TextFormat::EOL .
            '&ePlayers per faction &f' . FactionUtils::MAXIMUM_MEMBERS . '&7, &eAllies per faction &f' . FactionUtils::MAXIMUM_ALLIES . TextFormat::EOL .
            '&7&m--------------------------------';
    }
}