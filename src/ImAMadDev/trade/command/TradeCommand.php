<?php

namespace ImAMadDev\trade\command;

use ImAMadDev\command\Command;
use ImAMadDev\manager\TradeManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\player\modules\TradeRequest;
use ImAMadDev\texts\command\subCommands\CreateSubCommand;
use ImAMadDev\texts\command\subCommands\EditSubCommand;
use ImAMadDev\texts\command\subCommands\ListSubCommand;
use ImAMadDev\texts\command\subCommands\DisbandSubCommand;
use ImAMadDev\texts\command\subCommands\TPHereSubCommand;
use ImAMadDev\trade\TradeSession;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TradeCommand extends Command {
	
	public function __construct() {
		parent::__construct("trade", "Manage trades", "/trade help <1-5>");
		$this->addSubCommand(new CreateSubCommand());
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new DisbandSubCommand());
        $this->addSubCommand(new EditSubCommand());
		$this->addSubCommand(new TPHereSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if ($sender instanceof HCFPlayer) {
            if (isset($args[0])) {
                if ($args[0] === "accept"){
                    if (isset($args[1])) {
                        $sender_ = Server::getInstance()->getPlayerByPrefix($args[1]);
                        foreach (TradeManager::getInstance()->getRequests() as $request) {
                            if ($request->isSender($sender_) and $request->isReceiver($sender)){
                                var_dump("Holaaa");
                                $request->continue();
                                return;
                            }
                        }
                    }
                } elseif ($args[0] === "deny"){
                    if (isset($args[1])) {
                        $sender_ = Server::getInstance()->getPlayerByPrefix($args[1]);
                        foreach (TradeManager::getInstance()->getRequests() as $request) {
                            if ($request->isSender($sender_) and $request->isReceiver($sender)){
                                $request->cancel();
                                return;
                            }
                        }
                    }
                } else {
                    $receiver = Server::getInstance()->getPlayerByPrefix($args[0]);
                    if ($receiver instanceof HCFPlayer) {
                        if (TradeManager::getInstance()->hasPlayersRequest($sender->getName(), $receiver->getName())) {
                            return;
                        }
                        TradeManager::getInstance()->addRequest(new TradeRequest($sender, $receiver));
                    }
                }
            }
        }
	}
}