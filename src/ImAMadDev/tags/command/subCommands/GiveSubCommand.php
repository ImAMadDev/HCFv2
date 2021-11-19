<?php

namespace ImAMadDev\tags\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\player\PlayerData;
use ImAMadDev\tags\Tag;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GiveSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("give", "/tag give (player name) (tag)", ["set"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission("tag.command")){
            $sender->sendMessage(TextFormat::RED . "You doesnt have permissions to do this!");
            return;
        }
        if (!empty($args[1])){
            if (($player = Server::getInstance()->getPlayerByPrefix($args[1])) instanceof HCFPlayer) {
                if (empty($args[2])){
                    $sender->sendMessage(TextFormat::RED . $this->getUsage());
                    return;
                }
                if (($tag = HCF::getTagManager()->getTag($args[2])) instanceof Tag) {
                    if (PlayerData::hasTag($player->getName(), $tag->getName())){
                        $sender->sendMessage(TextFormat::RED . "This player already have this tag.");
                        return;
                    }
                    PlayerData::addTag($player->getName(), $tag->getName());
                    $sender->sendMessage(TextFormat::GRAY . "You have given the {$tag->getFormat()} " . TextFormat::GRAY . "tag to " . TextFormat::GOLD . $player->getName());
                    $player->sendMessage(TextFormat::GRAY . "You have received the " . $tag->getFormat() . TextFormat::GREEN . " Tag");
                } else {
                    $sender->sendMessage(TextFormat::RED . "This tag doesnt exist.");
                }
            } else {
                if (PlayerData::hasData($args[1], "tags")){
                    if (empty($args[2])){
                        $sender->sendMessage(TextFormat::RED . $this->getUsage());
                        return;
                    }
                    if (($tag = HCF::getTagManager()->getTag($args[2])) instanceof Tag) {
                        if (PlayerData::hasTag($args[1], $tag->getName())){
                            $sender->sendMessage(TextFormat::RED . "This player already have this tag.");
                            return;
                        }
                        PlayerData::addTag($args[1], $tag->getName());
                        $sender->sendMessage(TextFormat::GRAY . "You have given the {$tag->getFormat()} " . TextFormat::GRAY . "tag to " . TextFormat::GOLD . $args[1]);
                    } else {
                        $sender->sendMessage(TextFormat::RED . "This tag doesnt exist.");
                    }
                }
            }
        }
    }
}