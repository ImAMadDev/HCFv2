<?php

namespace ImAMadDev\manager;

use ImAMadDev\crate\CrateCreateSession;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\InventoryUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\block\Block;
use pocketmine\item\ItemIds;
use pocketmine\utils\Config;
use ImAMadDev\HCF;
use ImAMadDev\crate\Crate;

use ImAMadDev\crate\types\{Basic, CustomCrate, KOTH, Eternal, Sapphire, Anubis, Vote, Cthulhu};

use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use function count;

class CrateManager {
    use SingletonTrait;

	/** @var Crate[] */
	private array $crates = [];

	/** @var HCF */
	private static HCF $main;

    /**
     * @var array
     */
    private static array $sessions = [];

	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$this->loadCrates();
	}


    /**
     * @return array
     */
    public function getSessions(): array
    {
        return self::$sessions;
    }

    public function openSession(HCFPlayer $player, string $name) : void
    {
        if($this->hasSession($player)) {
            $player->sendMessage(TextFormat::RED . "Error: you already have an open session");
            return;
        }
        self::$sessions[$player->getName()] = new CrateCreateSession($player, $name);
        $player->sendMessage(TextFormat::GREEN . "You have open a kit creator session, crate name $name");
    }

    public function closeSession(HCFPlayer $player) : void
    {
        if(!$this->hasSession($player)) {
            $player->sendMessage(TextFormat::RED . "Error: you dont have any open session");
            return;
        }
        unset(self::$sessions[$player->getName()]);
        $player->sendMessage(TextFormat::GREEN . "You have close your crate creator session");
    }

    #[Pure] public function getSession(HCFPlayer $player) : CrateCreateSession
    {
        return self::$sessions[$player->getName()];
    }

    #[Pure] public function hasSession(HCFPlayer $player) : bool
    {
        return in_array($player->getName(), array_keys(self::$sessions));
    }

	public function loadCrates() {
		$this->addCrate(new Basic());
		$this->addCrate(new KOTH());
		$this->addCrate(new Eternal());
		$this->addCrate(new Sapphire());
		$this->addCrate(new Anubis());
		$this->addCrate(new Cthulhu());
		$this->addCrate(new Vote());
		$this->loadCustomCrates();
		$this->getMain()->getLogger()->info("§aThe crate have been loaded! Number of crates: " . count($this->getCrates()));
	}

    public function removeCustomKit(string $name) : void
    {
        if ($this->getCrateByName($name) instanceof CustomCrate){
            unlink(self::$main->getDataFolder() . "crates" . DIRECTORY_SEPARATOR . $name . ".yml");
            unset($this->crates[$name]);
        }
    }

    public function loadCustomCrates() : void
    {
        if(!is_dir(self::$main->getDataFolder() . "crates")) mkdir(self::$main->getDataFolder() . "crates");
        foreach (glob(self::$main->getDataFolder() . "crates" . DIRECTORY_SEPARATOR . "*yml") as $file) {
            $config = new Config($file, Config::YAML);
            $inventory = InventoryUtils::decode($config->get("Inventory", ""));
            $key = $this->getKeyFromFile($config);
            $down_block = $this->getBlockFromFile($config);
            $crate = new CustomCrate(basename($file, ".yml"), $inventory, $config->get("CustomKey", strtoupper(basename($file, ".yml")) . "_KEY"), $config->get("customName", "&6" . basename($file, ".yml")), $key, $down_block);
            $this->addCrate($crate);
        }
	}

    /**
     * @param CrateCreateSession $session
     */
    public function createCustomCrate(CrateCreateSession $session) : void
    {
        $file = new Config(self::$main->getDataFolder() . "crates" . DIRECTORY_SEPARATOR . $session->getData()["name"] . ".yml", Config::YAML);
        foreach ($session->getData() as $k => $datum) {
            $file->set($k, $datum);
        }
        $file->save();
        $items = InventoryUtils::decode($file->get("Inventory", ""));
        $key = $this->getKeyFromFile($file);
        $down_block = $this->getBlockFromFile($file);
        $crate = new CustomCrate($session->getData()["name"], $items, $file->get("CustomKey", strtoupper($session->getData()["name"]) . "_KEY"), $file->get("customName", "&6" . $session->getData()["name"]), $key, $down_block);
        $this->addCrate($crate);
    }

    /**
     * @param Config $file
     * @return Item
     */
    public function getKeyFromFile(Config $file) : Item
    {
        $icon = explode(":", $file->get("key", ItemIds::DYE . ":0:1"));
        return ItemFactory::getInstance()->get($icon[0], $icon[1], $icon[2]);
    }

    /**
     * @param Config $file
     * @return Block
     */
    public function getBlockFromFile(Config $file) : Block
    {
        $block = explode(":", $file->get("down_block", BlockLegacyIds::BRICK_BLOCK . ":0:1"));
        return BlockFactory::getInstance()->get($block[0], $block[1], $block[2]);
    }

	public function addCrate(Crate $crate) {
		$this->crates[$crate->getName()] = $crate;
	}
	
	public function getCrateByName(string $name): ?Crate
    {
		foreach ($this->getCrates() as $crate) {
			if ($crate->isCrateName($name)) {
				return $crate;
			}
		}
		return null;
	}
	
	public function getCrateByBlock(Block $block): ?Crate
    {
		foreach ($this->getCrates() as $crate) {
			if ($crate->isCrate($block)) {
				return $crate;
			}
		}
		return null;
	}
	
	/**
	 * @return Crate[]
	 */
	public function getCrates(): array {
		return $this->crates;
	}
	
	public function getMain(): HCF {
		return self::$main;
	}

}
