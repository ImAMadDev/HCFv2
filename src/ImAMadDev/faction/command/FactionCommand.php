<?php

namespace ImAMadDev\faction\command;

use ImAMadDev\command\Command;
use ImAMadDev\faction\command\subCommands\AllySubCommand;
use ImAMadDev\faction\command\subCommands\MapSubCommand;
use ImAMadDev\faction\command\subCommands\ChatSubCommand;
use ImAMadDev\faction\command\subCommands\ClaimSubCommand;
use ImAMadDev\faction\command\subCommands\CreateSubCommand;
use ImAMadDev\faction\command\subCommands\DemoteSubCommand;
use ImAMadDev\faction\command\subCommands\DepositSubCommand;
use ImAMadDev\faction\command\subCommands\DisbandSubCommand;
use ImAMadDev\faction\command\subCommands\HelpSubCommand;
use ImAMadDev\faction\command\subCommands\HomeSubCommand;
use ImAMadDev\faction\command\subCommands\InfoSubCommand;
use ImAMadDev\faction\command\subCommands\SetDTRSubCommand;
use ImAMadDev\faction\command\subCommands\SetPointsSubCommand;
use ImAMadDev\faction\command\subCommands\SetKillsSubCommand;
use ImAMadDev\faction\command\subCommands\SetBalanceSubCommand;
use ImAMadDev\faction\command\subCommands\InviteSubCommand;
use ImAMadDev\faction\command\subCommands\JoinSubCommand;
use ImAMadDev\faction\command\subCommands\KickSubCommand;
use ImAMadDev\faction\command\subCommands\LeaderSubCommand;
use ImAMadDev\faction\command\subCommands\LeaveSubCommand;
use ImAMadDev\faction\command\subCommands\ListSubCommand;
use ImAMadDev\faction\command\subCommands\PromoteSubCommand;
use ImAMadDev\faction\command\subCommands\SetHomeSubCommand;
use ImAMadDev\faction\command\subCommands\StuckSubCommand;
use ImAMadDev\faction\command\subCommands\TopSubCommand;
use ImAMadDev\faction\command\subCommands\UnallySubCommand;
use ImAMadDev\faction\command\subCommands\UnclaimSubCommand;
use ImAMadDev\faction\command\subCommands\WithdrawSubCommand;
use ImAMadDev\faction\command\subCommands\ForceDisbandSubCommand;
use ImAMadDev\faction\command\subCommands\FocusSubCommand;
use ImAMadDev\faction\command\subCommands\UnFocusSubCommand;
use pocketmine\command\CommandSender;

class FactionCommand extends Command {
	
	public function __construct() {
		parent::__construct("faction", "Manage faction", "/faction help <1-5>", ["f"]);
		$this->addSubCommand(new AllySubCommand());
		$this->addSubCommand(new ChatSubCommand());
		$this->addSubCommand(new ClaimSubCommand());
		$this->addSubCommand(new CreateSubCommand());
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new DisbandSubCommand());
		$this->addSubCommand(new InfoSubCommand());
		$this->addSubCommand(new SetDTRSubCommand());
		$this->addSubCommand(new SetPointsSubCommand());
		$this->addSubCommand(new SetKillsSubCommand());
		$this->addSubCommand(new SetBalanceSubCommand());
		$this->addSubCommand(new MapSubCommand());
		$this->addSubCommand(new SetHomeSubCommand());
		$this->addSubCommand(new FocusSubCommand());
		$this->addSubCommand(new UnclaimSubCommand());
		$this->addSubCommand(new HomeSubCommand());
		$this->addSubCommand(new DemoteSubCommand());
		$this->addSubCommand(new DepositSubCommand());
		$this->addSubCommand(new HelpSubCommand());
		$this->addSubCommand(new InviteSubCommand());
		$this->addSubCommand(new JoinSubCommand());
		$this->addSubCommand(new KickSubCommand());
		$this->addSubCommand(new StuckSubCommand());
		$this->addSubCommand(new LeaderSubCommand());
		$this->addSubCommand(new LeaveSubCommand());
		$this->addSubCommand(new PromoteSubCommand());
		$this->addSubCommand(new TopSubCommand());
		$this->addSubCommand(new UnallySubCommand());
		$this->addSubCommand(new WithdrawSubCommand());
		$this->addSubCommand(new ForceDisbandSubCommand());
		$this->addSubCommand(new UnFocusSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage("/faction help <1-5>");
			return;
		}
		$sender->sendMessage("/faction help <1-5>");
	}
}