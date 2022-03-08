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

use ImAMadDev\crate\types\{Basic, CustomCrate, KOTH, Cupcake, Icecream, Waffle, Vote};

use pocketmine\utils\Filesystem;
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
		//$this->addCrate(new Cupcake());
		//$this->addCrate(new Icecream());
		$this->addCrate(new Waffle());
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
            $contents = yaml_parse(Config::fixYAMLIndexes(file_get_contents($file)));
            $inventory = InventoryUtils::decode(base64_decode($contents["Inventory"] ?? ""));
            $key = $this->getKeyFromFile($contents['key']);
            $down_block = $this->getBlockFromFile($contents['down_block']);
            $crate = new CustomCrate(basename($file, ".yml"), $inventory, $contents["customName"] ?? "&6" . $contents["name"], $key, $down_block->asItem());
            $this->addCrate($crate);
        }
	}

    /**
     * @param CrateCreateSession $session
     */
    public function createCustomCrate(CrateCreateSession $session) : void
    {
        Filesystem::safeFilePutContents(self::$main->getDataFolder() . "crates/" . $session->getData()['name'] . ".yml", yaml_emit($session->getData(), YAML_UTF8_ENCODING));
        $items = InventoryUtils::decode(base64_decode($session->getData()["Inventory"] ?? ''));
        $key = $this->getKeyFromFile($session->getData()['key']);
        $down_block = $this->getBlockFromFile($session->getData()['down_block']);
        $crate = new CustomCrate($session->getData()["name"], $items, $session->getData()["customName"] ?? "&6" . $session->getData()["name"], $key, $down_block->asItem());
        $this->addCrate($crate);
    }

    /**
     * @param string $value
     * @return Item
     */
    public function getKeyFromFile(string $value) : Item
    {
        $icon = explode(":", $value ?? ItemIds::DYE . ":0:1");
        return ItemFactory::getInstance()->get($icon[0], $icon[1], $icon[2]);
    }

    /**
     * @param string $value
     * @return Block
     */
    public function getBlockFromFile(string $value) : Block
    {
        $block = explode(":", $value ?? BlockLegacyIds::BRICK_BLOCK . ":0:1");
        return BlockFactory::getInstance()->get($block[0], $block[1], $block[2]);
    }

	public function addCrate(Crate $crate) {
		$this->crates[$crate->getName()] = $crate;
	}
	
	#[Pure] public function getCrateByName(string $name): ?Crate
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
