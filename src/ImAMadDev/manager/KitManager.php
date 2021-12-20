<?php

namespace ImAMadDev\manager;

use ImAMadDev\kit\classes\ArcherClass;
use ImAMadDev\kit\classes\BardClass;
use ImAMadDev\kit\classes\IClass;
use ImAMadDev\kit\classes\MageClass;
use ImAMadDev\kit\classes\MinerClass;
use ImAMadDev\kit\classes\RogueClass;
use ImAMadDev\kit\KitCreatorSession;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\InventoryUtils;
use JetBrains\PhpStorm\Pure;
use JsonException;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Config;
use ImAMadDev\HCF;
use ImAMadDev\kit\Kit;

use ImAMadDev\kit\types\{CustomKit,
    Miner,
    Eternal,
    Cthulhu,
    Sapphire,
    Anubis,
    Mage,
    Diamond,
    Builder,
    Bard,
    Starter,
    Rogue,
    Archer};

use pocketmine\utils\Filesystem;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use function count;

class KitManager {
    use SingletonTrait;

	/**
     * @var Kit[] $kits
     */
	private array $kits = [];

    /**
     * @var IClass[] $classes
     */
    private array $classes = [];

	/**
     * @var HCF|null
     */
	private static ?HCF $main = null;

    /**
     * @var array $sessions
     */
    private static array $sessions = [];

	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$this->loadKits();
        $this->loadClasses();
	}

    /**
     * @return array
     */
    public function getSessions(): array
    {
        return self::$sessions;
    }

    /**
     * @param HCFPlayer $player
     * @param string $name
     */
    public function openSession(HCFPlayer $player, string $name) : void
    {
        if($this->hasSession($player)) {
            $player->sendMessage(TextFormat::RED . "Error: you already have an open session");
            return;
        }
        self::$sessions[$player->getName()] = new KitCreatorSession($player, $name);
        $player->sendMessage(TextFormat::GREEN . "You have open a kit creator session, kit name $name");
    }

    /**
     * @param HCFPlayer $player
     */
    public function closeSession(HCFPlayer $player) : void
    {
        if(!$this->hasSession($player)) {
            $player->sendMessage(TextFormat::RED . "Error: you dont have any open session");
            return;
        }
        unset(self::$sessions[$player->getName()]);
        $player->sendMessage(TextFormat::GREEN . "You have close your kit creator session");
    }

    /**
     * @param HCFPlayer $player
     * @return KitCreatorSession
     */
    #[Pure] public function getSession(HCFPlayer $player) : KitCreatorSession
    {
        return self::$sessions[$player->getName()];
    }

    /**
     * @param HCFPlayer $player
     * @return bool
     */
    #[Pure] public function hasSession(HCFPlayer $player) : bool
    {
        return in_array($player->getName(), array_keys(self::$sessions));
    }

	public function loadKits() {
		$this->addKit(new Archer());
		$this->addKit(new Builder());
		$this->addKit(new Mage());
		$this->addKit(new Miner());
		$this->addKit(new Diamond());
		$this->addKit(new Bard());
		$this->addKit(new Starter());
		$this->addKit(new Rogue());
		$this->addKit(new Sapphire());
		$this->addKit(new Eternal());
		$this->addKit(new Cthulhu());
		$this->addKit(new Anubis());
		$this->loadCustomKits();
		$this->getMain()->getLogger()->info("§aThe kits have been loaded! Number of kits: " . count($this->getKits()));
	}

    public function loadCustomKits() : void
    {
        if (!is_dir(self::$main->getDataFolder() . "kits")) @mkdir(self::$main->getDataFolder() . "kits");
        foreach(glob(self::$main->getDataFolder() . "kits" . DIRECTORY_SEPARATOR . "*.json") as $file){
            $contents = json_decode(file_get_contents($file), true);
            $armor = InventoryUtils::decode($contents["Armor"] ?? "", "Armor");
            $items = InventoryUtils::decode($contents["Inventory"] ?? "", "");
            $kit = new CustomKit(basename($file, ".json"), $contents["permission"] ?? basename($file, ".json") . ".kit", $armor, $items, $this->getIconFromFile($contents['icon']), $contents["description"] ?? "Example kit", $contents["countdown"] ?? 172800, $contents["customName"] ?? "&6" . basename($file, ".json"), $contents["slot"] ?? 0);
            $this->addKit($kit);
        }
	}

    public function loadClasses() : void
    {
        $this->addClass(new ArcherClass());
        $this->addClass(new BardClass());
        $this->addClass(new MinerClass());
        $this->addClass(new MageClass());
        $this->addClass(new RogueClass());
    }

    /**
     * @param string $name
     */
    public function removeCustomKit(string $name) : void
    {
        if (($kit = $this->getKitByName($name)) instanceof CustomKit){
           @unlink(self::$main->getDataFolder() . "kits" . DIRECTORY_SEPARATOR . $kit->getName() . ".json");
            unset($this->kits[$name]);
        }
	}

    /**
     * @param KitCreatorSession $session
     */
	public function createCustomKit(KitCreatorSession $session) : void
    {
        Filesystem::safeFilePutContents(self::$main->getDataFolder() . "kits/" . $session->getData()["name"] . ".json", json_encode($session->getData(), JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
        $armor = InventoryUtils::decode($session->getData()["Armor"] ?? "", "Armor");
        $items = InventoryUtils::decode($session->getData()["Inventory"] ?? "");
        $kit = new CustomKit($session->getData()["name"], $session->getData()["permission"] ?? $session->getData()["name"] . ".kit", $armor, $items, $this->getIconFromFile($session->getData()['icon']), $session->getData()["description"] ?? "Example kit", $session->getData()["countdown"] ?? 172800, $session->getData()["customName"] ?? "&6" . $session->getData()["name"], $session->getData()["slot"] ?? 0);
        $this->addKit($kit);
    }

    /**
     * @param string $value
     * @return Item
     */
    public function getIconFromFile(string $value) : Item
    {
        $icon = explode(":", $value);
        return ItemFactory::getInstance()->get($icon[0], $icon[1], $icon[2]);
	}

    /**
     * @param Kit $kit
     */
    public function addKit(Kit $kit) {
		$this->kits[$kit->getName()] = $kit;
	}

    /**
     * @param string $name
     * @return Kit|null
     */
    public function getKitByName(string $name): ?Kit
    {
		foreach ($this->getKits() as $kit) {
			if ($kit->isKit($name)) {
				return $kit;
			}
		}
		return null;
	}
	
	/**
	 * @return Kit[]
	 */
	public function getKits(): array {
		return $this->kits;
	}

    /**
     * @return HCF
     */
    public function getMain(): HCF {
		return self::$main;
	}

	/**
	 * @return Item[]
	 */
	public function getAllKitItems(): array {
		$items = [];
		foreach ($this->getKits() as $kit) {
			$items[] = $kit->getIcon();
		}
		return $items;
	}

    /**
     * @param IClass $class
     * @return void
     */
    public function addClass(IClass $class) : void
    {
        $this->classes[$class->name] = $class;
    }

    /**
     * @return IClass[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getClassByInventory(ArmorInventory $armorInventory): ?IClass
    {
        foreach ($this->getClasses() as $class) {
            if ($class->isThis($armorInventory)) {
                return $class;
            }
        }
        return null;
    }

}
