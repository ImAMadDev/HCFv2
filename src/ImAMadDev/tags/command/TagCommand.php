<?php

namespace ImAMadDev\tags\command;

use formapi\SimpleForm;
use ImAMadDev\command\Command;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\player\PlayerData;
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

    private function getTagsMenu(Player $player){
        $form = new SimpleForm(function (Player $player, $data) {
            if($data === null){
                return;
            }
            if(($tag = HCF::getTagManager()->getTag($data)) instanceof Tag) {
                if(PlayerData::hasTag($player->getName(), $data)){
                    $player->setCurrentTag($tag->getName());
                    PlayerData::selectTag($player->getName(), $tag->getName());
                    $player->sendMessage(TextFormat::colorize("&aYou have selected the tag: ") . $tag->getFormat());
                } else {
                    $player->sendMessage(TextFormat::RED . "You dont have this tag: {$tag->getFormat()}");
                }
            } else {
                $player->sendMessage(TextFormat::RED . "An unknown error occurred while trying to execute this action.");
            }
        });
        $form->setTitle(TextFormat::GREEN . "Your tags");
        foreach(PlayerData::getData($player->getName())->get("tags", []) as $tag) {
            if (($tagClass = HCF::getTagManager()->getTag($tag)) instanceof Tag) {
                $form->addButton($tagClass->getFormat(), -1, "", $tagClass->getName());
            } else {
                PlayerData::removeTag($player->getName(), $tag);
            }
        }
        $form->sendToPlayer($player);
    }
}