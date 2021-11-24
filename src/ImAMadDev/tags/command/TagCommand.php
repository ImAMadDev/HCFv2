<?php

namespace ImAMadDev\tags\command;

use formapi\SimpleForm;
use ImAMadDev\command\Command;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\tags\command\subCommands\GiveSubCommand;
use ImAMadDev\tags\Tag;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TagCommand extends Command
{

    public function __construct()
    {
        parent::__construct("tag", "Tag command", "/tag give (player name) (tag)", []);
        $this->addSubCommand(new GiveSubCommand());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(isset($args[0])) {
            $subCommand = $this->getSubCommand($args[0]);
            if($subCommand !== null) {
                $subCommand->execute($sender, $commandLabel, $args);
                return;
            }
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        } else {
            if($sender instanceof HCFPlayer){
                $this->getTagsMenu($sender);
            } else {
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
            }
        }
    }

    private function getTagsMenu(Player $player)
    {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }
            if ($player instanceof HCFPlayer) {
                if (($tag = HCF::getTagManager()->getTag($data)) instanceof Tag) {
                    if ($player->getCache()->hasDataInArray($data, 'tags')) {
                        $player->setCurrentTag($tag->getName());
                        $player->getCache()->setInData('currentTag', $tag->getName());
                        $player->sendMessage(TextFormat::colorize("&aYou have selected the tag: ") . $tag->getFormat());
                    } else {
                        $player->sendMessage(TextFormat::RED . "You dont have this tag: {$tag->getFormat()}");
                    }
                } else {
                    $player->sendMessage(TextFormat::RED . "An unknown error occurred while trying to execute this action.");
                }
            }
        });
        if ($player instanceof HCFPlayer) {
            $form->setTitle(TextFormat::GREEN . "Your tags");
            $tags = $player->getCache()->getInData('tags') ?? [];
            foreach ($tags as $tag) {
                if (($tagClass = HCF::getTagManager()->getTag($tag)) instanceof Tag) {
                    $form->addButton($tagClass->getFormat(), -1, "", $tagClass->getName());
                } else {
                    $player->getCache()->removeInArray('tags', $tag);
                }
            }
            $form->sendToPlayer($player);
        }
    }
}