<?php

namespace ImAMadDev;

use ImAMadDev\customenchants\CustomEnchantments;
use ImAMadDev\inventory\EnderChestInvMenuType;
use ImAMadDev\youtubers\redeem\RedeemManager;
use JetBrains\PhpStorm\Pure;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;

use ImAMadDev\manager\{
    FactionManager,
    TagManager,
    TextsManager,
    EventsManager,
    KOTHManager,
    CrateManager,
    AbilityManager,
    KitManager,
    EOTWManager,
    BlockManager,
    ItemManager,
    SOTWManager,
    EntityManager,
    ClaimManager,
    RankManager,
    CommandManager};
use ImAMadDev\listener\HCFListener;
use ImAMadDev\listener\projectile\ProjectileListener;
use ImAMadDev\faction\FactionListener;
use ImAMadDev\listener\anticheat\{BuggyListener, ReachModule, CpsModule};
use ImAMadDev\tile\Tile;
use ImAMadDev\crate\CrateListener;
use ImAMadDev\ability\AbilityListener;
use ImAMadDev\kit\KitListener;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\ticks\{ClearLagTick, BroadcastTick};
use CombatLogger\CombatManager;
use pocketmine\world\generator\hell\Nether;
use pocketmine\world\WorldCreationOptions;
use scoreboard\Scoreboard;
use muqsit\invmenu\InvMenuHandler;

class HCF extends PluginBase {
	
	public static HCF $instance;
	
	public static FactionManager $factionManager;
	
	public static ClaimManager $claimManager;
	
	public static CommandManager $commandManager;
	
	public static RankManager $rankManager;
	
	public static KitManager $kitManager;
	
	public static CrateManager $crateManager;
	
	public static CombatManager $combatManager;
	
	public static AbilityManager $abilityManager;
	
	public static KOTHManager $KOTHManager;

    public static EventsManager $EventsManager;
	
	public static TextsManager $textsManager;

    public static TagManager $tagManager;

    private static RedeemManager $redeemManager;

    public const INV_MENU_TYPE_ENDER_CHEST = "hcf:enderchest";

    public function onLoad(): void{
		$this->loadInstances();
        //PermissionManager::getInstance()->addPermission(new Permission("hol.add", "command", false));
	}
	
	public function onEnable(): void{
		date_default_timezone_set('America/Guayaquil'); 
		//Tile::init();
		if (!is_dir($this->getDataFolder() . "copied_skins/")) @mkdir($this->getDataFolder() . "copied_skins/");
		if(!is_dir($this->getDataFolder() . "players/")) @mkdir($this->getDataFolder() . "players/");
		$this->getServer()->getPluginManager()->registerEvents(new EOTWManager($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new SOTWManager($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new HCFListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new FactionListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new ProjectileListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new CrateListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new AbilityListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new CpsModule($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new ReachModule($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new KitListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BuggyListener(), $this);
		$this->getScheduler()->scheduleRepeatingTask(new ClearLagTick(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTick($this), 4800);
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
        InvMenuHandler::getTypeRegistry()->register(self::INV_MENU_TYPE_ENDER_CHEST, new EnderChestInvMenuType());
        CustomEnchantments::init();
		$this->getServer()->getNetwork()->setName(TextFormat::colorize("&6HCF"));
        if (!$this->getServer()->getWorldManager()->isWorldGenerated(HCFUtils::NETHER_MAP)){
            $g = WorldCreationOptions::create();
            $g->setGeneratorClass(Nether::class);
            $g->setSpawnPosition(new Vector3(0, 100, 0));
            $this->getServer()->getWorldManager()->generateWorld(HCFUtils::NETHER_MAP, $g);
        } else {
            Server::getInstance()->getWorldManager()->loadWorld(HCFUtils::NETHER_MAP);
        }
        Server::getInstance()->getWorldManager()->loadWorld(HCFUtils::DEFAULT_MAP);
		self::$textsManager = new TextsManager($this);
	}

	private function loadInstances() : void {
		self::$instance = $this;
		self::$claimManager = new ClaimManager($this);
		self::$factionManager = new FactionManager($this);
		self::$rankManager = new RankManager($this);
		self::$kitManager = new KitManager($this);
		self::$crateManager = new CrateManager($this);
		self::$commandManager = new CommandManager($this);
		self::$combatManager = new CombatManager($this);
		self::$abilityManager = new AbilityManager($this);
		self::$KOTHManager = new KOTHManager($this);
		self::$EventsManager = new EventsManager($this);
        self::$tagManager = new TagManager($this);
        self::$redeemManager = new RedeemManager($this);
		new EntityManager($this);
		new BlockManager($this);
		new ItemManager($this);
	}
	
	public static function getInstance() : self {
		return self::$instance;
	}
	
	public static function getClaimManager() : ClaimManager {
		return self::$claimManager;
	}
	
	public static function getFactionManager() : FactionManager {
		return self::$factionManager;
	}
	
	public static function getRankManager() : RankManager {
		return self::$rankManager;
	}
	
	public function getCommandManager() : CommandManager {
		return self::$commandManager;
	}
	
	public function getKitManager() : KitManager {
		return self::$kitManager;
	}
	
	public function getCrateManager() : CrateManager {
		return self::$crateManager;
	}
	
	public function getCombatManager() : CombatManager {
		return self::$combatManager;
	}
	
	public function getAbilityManager() : AbilityManager {
		return self::$abilityManager;
	}
	
	public function getKOTHManager() : KOTHManager {
		return self::$KOTHManager;
	}
	
	#[Pure] public static function getScoreboard() : Scoreboard {
		return new Scoreboard();
	}

    /**
     * @return TagManager
     */
    public static function getTagManager(): TagManager
    {
        return self::$tagManager;
    }

    /**
     * @return RedeemManager
     */
    public static function getRedeemManager(): RedeemManager
    {
        return self::$redeemManager;
    }

	public function  getTopKills() : array {
	    $kills = [];
        foreach (glob($this->getDataFolder() . "players/" . "*.js") as $file) {
	        $kills[basename($file, ".js")] = json_decode(file_get_contents($file), true)["kills"];
	    }
	    arsort($kills);
	    $top = 0;
	    $killers = [];
	    foreach($kills as $name => $count){
	        if($count <= 0 || $top === 10) break;
	        $top++;
	        $killers[] = ["name" => $name, "kills" => $count];
	    }
	    return $killers;
    }
}