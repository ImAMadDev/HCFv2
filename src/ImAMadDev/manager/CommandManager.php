<?php

namespace ImAMadDev\manager;

use ImAMadDev\staff\command\{
	StaffCommand,
	SudoCommand
};
use ImAMadDev\tags\command\TagCommand;
use ImAMadDev\trade\command\TradeCommand;
use ImAMadDev\youtubers\redeem\command\RedeemCommand;
use pocketmine\command\Command;
use pocketmine\plugin\PluginException;

use ImAMadDev\faction\command\FactionCommand;
use ImAMadDev\rank\command\RankCommand;
use ImAMadDev\command\defaults\{CapeCommand,
    HomeCommand,
    LFFCommand,
    MapKit,
    Rally,
    SpawnCommand,
    RollbackCommand,
    ParticleCommand,
    FixCommand,
    LogoutCommand,
    ClaimShopCommand,
    EnderChestCommand,
    RenameCommand,
    TLCommand,
    TellCommand,
    ShopCommand,
    ReclaimCommand,
    VoteCommand,
    NearCommand,
    SetBalanceCommand,
    BalanceCommand,
    EOTWCommand,
    SOTWCommand,
    PurgeCommand,
    ListCommand,
    PvPCommand};
use ImAMadDev\claim\command\ClaimCommand;
use ImAMadDev\kit\command\KitCommand;
use ImAMadDev\crate\command\CrateCommand;
use ImAMadDev\ability\command\AbilityCommand;
use ImAMadDev\koth\command\KOTHCommand;
use ImAMadDev\npc\command\NPCCommand;
use ImAMadDev\events\command\EventCommand;
use ImAMadDev\texts\command\TextCommand;
use ImAMadDev\HCF;

class CommandManager {
	
	private static ?HCF $main = null;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->unregisterCommand("tell");
		$this->unregisterCommand("about");
		$this->unregisterCommand("me");
		$this->unregisterCommand("particle");
		$this->unregisterCommand("title");
		$this->unregisterCommand("list");
		$this->registerCommand(new FactionCommand());
		$this->registerCommand(new ParticleCommand());
		$this->registerCommand(new LogoutCommand());
		$this->registerCommand(new RankCommand());
		$this->registerCommand(new SpawnCommand());
		$this->registerCommand(new RollbackCommand());
		$this->registerCommand(new SOTWCommand());
		$this->registerCommand(new EOTWCommand());
		$this->registerCommand(new ClaimCommand());
		$this->registerCommand(new TextCommand());
		$this->registerCommand(new KitCommand());
		$this->registerCommand(new CrateCommand());
		$this->registerCommand(new AbilityCommand());
		$this->registerCommand(new KOTHCommand());
		$this->registerCommand(new PvPCommand());
		$this->registerCommand(new NPCCommand());
		$this->registerCommand(new BalanceCommand());
		$this->registerCommand(new SetBalanceCommand());
		$this->registerCommand(new NearCommand());
		$this->registerCommand(new VoteCommand());
		$this->registerCommand(new ReclaimCommand());
		$this->registerCommand(new EventCommand());
		$this->registerCommand(new ShopCommand());
		$this->registerCommand(new TellCommand());
		$this->registerCommand(new TLCommand());
		$this->registerCommand(new RenameCommand());
		$this->registerCommand(new FixCommand());
		$this->registerCommand(new EnderChestCommand());
		$this->registerCommand(new ClaimShopCommand());
		$this->registerCommand(new HomeCommand());
		$this->registerCommand(new Rally());
		$this->registerCommand(new MapKit());
		$this->registerCommand(new LFFCommand());
        $this->registerCommand(new TagCommand());
        $this->registerCommand(new ListCommand());
        $this->registerCommand(new RedeemCommand());
        $this->registerCommand(new StaffCommand());
        $this->registerCommand(new SudoCommand());
        $this->registerCommand(new PurgeCommand());
	}
	
	public function registerCommand(Command $command): void {
		$commandMap = self::$main->getServer()->getCommandMap();
		$existingCommand = $commandMap->getCommand($command->getName());
		if($existingCommand !== null) {
			$commandMap->unregister($existingCommand);
		}
		$commandMap->register($command->getName(), $command);
	}
	
	public function unregisterCommand(string $name): void {
		$commandMap = self::$main->getServer()->getCommandMap();
		$command = $commandMap->getCommand($name);
		if($command === null) {
			throw new PluginException("Invalid command: $name to un-register.");
		}
		$commandMap->unregister($commandMap->getCommand($name));
	}
}