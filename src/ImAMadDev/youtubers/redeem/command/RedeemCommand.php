<?php /** @noinspection ALL */

namespace ImAMadDev\youtubers\redeem\command;

use ImAMadDev\ability\Ability;
use ImAMadDev\command\Command;
use ImAMadDev\HCF;
use ImAMadDev\player\PlayerData;
use ImAMadDev\youtubers\redeem\command\subCommand\AddSubCommand;
use ImAMadDev\youtubers\redeem\RedeemCreator;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class RedeemCommand extends Command
{

    public function __construct()
    {
        parent::__construct("redeem", "support a content creator!", "/redeem (content creator)", ["support"]);
        $this->addSubCommand(new AddSubCommand());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(isset($args[0])) {
            $subCommand = $this->getSubCommand($args[0]);
            if($subCommand !== null) {
                $subCommand->execute($sender, $commandLabel, $args);
            } else {
                if ($sender->getCache()->getInData('redeem', true, null) !== null){
                    $sender->sendMessage(TextFormat::RED . "You have already supported a partner!");
                    return;
                }
                if(($redeem = HCF::getRedeemManager()->getRedeem($args[0])) instanceof RedeemCreator){
                    $redeem->addClaim(1);
                    if (($partner = HCF::$abilityManager->getAbilityByName("PartnerPackages")) instanceof  Ability){
                        $partner->obtain($sender, 1);
                    }
                    $sender->getCache()->setInData('redeem', $redeem->getCreator());
                    Server::getInstance()->broadcastMessage(TextFormat::colorize("&6{$sender->getName()} &7used &6(/{$commandLabel} {$args[0]}) &7and got a partner package"));
                    return;
                } else {
                    $sender->sendMessage(TextFormat::RED . $args[0] . " is not registered as a partner on the server");
                    return;
                }
            }
        } else {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        }
    }
}