<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ticks\CheckVoteTick;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class VoteCommand extends Command {
	
    public function __construct() {
        parent::__construct("vote", "Check if you've voted yet.", "/vote");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "You don't have permission to do this action!");
            return;
        }
        if($sender->hasVoted()) {
            $sender->sendMessage(TextFormat::RED . "You have already claimed your vote!");
            return;
        }
        if($sender->isCheckingForVote()) {
            $sender->sendMessage(TextFormat::RED . "You already have a processing vote check!");
            return;
        }
        $this->getServer()->getAsyncPool()->submitTaskToWorker(new CheckVoteTick($sender->getName()), 1);
    }
}